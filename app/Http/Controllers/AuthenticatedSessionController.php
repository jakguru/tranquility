<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\User;
use Session;
use \App\AuthenticatedSession;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use \App\Realtime\RealtimeEvent;
use Illuminate\Support\Facades\URL;
use \App\Helpers\AjaxFeedbackHelper;
use \App\Jobs\RemoveEventsFromQueue;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    protected $session;
    protected $user;
    protected $ip;
    protected $authsession;

    public function __construct()
    {
        $this->session = Session::getId();
        $this->user = request()->user();
        $this->ip = request()->ip();
        $this->authsession = $this->getAuthSession();
    }

    protected function getAuthSession()
    {
        if (!is_a($this->user, '\App\User')) {
            return null;
        }
        switch (config('app.rtu.storage')) {
            case 'database':
                $s = AuthenticatedSession::where([
                    'user_id' => $this->user->id,
                    'session_id' => $this->session,
                ])->first();
                break;
        }
        return (isset($s) && is_a($s, '\App\AuthenticatedSession')) ? $s : null;
    }

    protected function createAuthSession()
    {
        switch (config('app.rtu.storage')) {
            case 'database':
                $s = new AuthenticatedSession();
                $s->user()->associate($this->user);
                $s->session_id = $this->session;
                $s->ip = $this->ip;
                $s->status = 'online';
                $s->last_active = Carbon::now();
                $s->events = [];
                $s->save();
                break;
        }
    }

    protected function updateAuthSessionStatus($status = 'online', $updateActiveTime = false)
    {
        $status = strtolower($status);
        $possible = ['offline', 'online', 'dnd'];
        if (in_array($status, $possible) && is_a($this->authsession, '\App\AuthenticatedSession')) {
            $this->authsession->status = $status;
            if (true == $updateActiveTime) {
                $this->authsession->last_active = Carbon::now();
            }
            switch (config('app.rtu.storage')) {
                case 'database':
                    $this->authsession->save();
                    break;
            }
        }
    }

    protected function removeAuthSession()
    {
        if (is_a($this->authsession, '\App\AuthenticatedSession')) {
            switch (config('app.rtu.storage')) {
                case 'database':
                    $this->authsession->delete();
                    break;
            }
        }
    }

    protected function getCurrentSessionEvents($remove = false)
    {
        if (!is_a($this->user, '\App\User')) {
            return [];
        }
        if (!is_a($this->authsession, '\App\AuthenticatedSession')) {
            return [];
        }
        $return = $this->authsession->events;
        foreach ($return as $hash => $event) {
            $event['hash'] = $hash;
            $return[$hash] = $event;
        }
        if (true == $remove && count($return) > 0) {
            RemoveEventsFromQueue::dispatch($this->user, array_keys($return));
        }
        return array_values($return);
    }

    public static function onPolling(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(401);
        }
        $c = get_called_class();
        $obj = new $c();
        $obj->updateAuthSessionStatus('online', true);
        $return = new \stdClass();
        $return->poll = URL::temporarySignedRoute('rtu', now()->addMinutes(1));
        $return->events = $obj->getCurrentSessionEvents(true);
        $return->messages = 0;
        $now = \Carbon\Carbon::now();
        $eod = \Carbon\Carbon::now()->setTime(23, 59, 59);
        $appointments = MyController::getMyAppointmentsBetweenDates($now, $eod);
        $return->appointments = [];
        foreach ($appointments as $appt) {
            array_push($return->appointments, [
                'subject' => $appt->subject,
                'own' => ($request->user()->id == $appt->owner_id),
                'start' => $request->user()->formatDateTime($appt->starts_at),
                'end' => $request->user()->formatDateTime($appt->ends_at),
                'url' => route('view-meeting', ['id' => $appt->id]),
            ]);
        }
        return AjaxFeedbackHelper::successResponse($return);
    }

    public static function onLogin()
    {
        $c = get_called_class();
        $obj = new $c();
        $obj->createAuthSession();
    }

    public static function onLogout()
    {
        $c = get_called_class();
        $obj = new $c();
        $obj->removeAuthSession();
    }

    public static function updateAFKStatuses()
    {
        switch (config('app.rtu.storage')) {
            case 'database':
                $afks = AuthenticatedSession::where('status', '<>', 'offline')
                        ->where('last_active', '<', Carbon::now()->subMinutes(2))->get();
                $expireds = AuthenticatedSession::where('status', 'like', 'offline')
                        ->where('last_active', '<', Carbon::now()->subDays(1))->get();
                foreach ($afks as $afk) {
                    $afk->status = 'offline';
                    $afk->save();
                }
                foreach ($expireds as $exp) {
                    $exp->delete();
                }
                break;
        }
    }

    public static function getCurrentUserSession()
    {
        $session = Session::getId();
        $user = request()->user();
        switch (config('app.rtu.storage')) {
            case 'database':
                $s = AuthenticatedSession::where([
                    'user_id' => $user->id,
                    'session_id' => $session,
                ])->first();
                break;
        }
        return (isset($s) && is_a($s, '\App\AuthenticatedSession')) ? $s : null;
    }

    public static function getUserSessions(User $user)
    {
        switch (config('app.rtu.storage')) {
            case 'database':
                return AuthenticatedSession::where('status', '<>', 'offline')
                       ->where('user_id', 'like', $user->id)
                       ->get();
                break;
        }
    }

    public static function emitToUserSession(User $user, RealtimeEvent $event, AuthenticatedSession $session)
    {
        $success = false;
        switch (config('app.rtu.storage')) {
            case 'database':
                $events = $session->events;
                if (!is_array($events)) {
                    $events = [];
                }
                $hash = $event->getHash();
                $events[$hash] = $event;
                $session->events = $events;
                $session->save();
                $success = true;
                break;
        }
        return $success;
    }

    public static function emitToUserSessions(User $user, RealtimeEvent $event)
    {
        Log::info(sprintf('Emitting "%s" to user %d', json_encode($event), $user->id));
        $sessions = self::getUserSessions($user);
        $success = false;
        foreach ($sessions as $session) {
            switch (config('app.rtu.storage')) {
                case 'database':
                    self::emitToUserSession($user, $event, $session);
                    break;
            }
        }
        return $success;
    }

    public static function removeEventFromSessions(User $user, $event)
    {
        if (is_a($event, 'RealtimeEvent')) {
            $event = $event->getHash();
        } elseif (is_object($event)) {
            return false;
        }
        $sessions = self::getUserSessions($user);
        $success = false;
        foreach ($sessions as $session) {
            switch (config('app.rtu.storage')) {
                case 'database':
                    $events = $session->events;
                    if (!is_array($events)) {
                        $events = [];
                    }
                    if (array_key_exists($event, $events)) {
                        unset($events[$event]);
                    }
                    $session->events = $events;
                    $session->save();
                    $success = true;
                    break;
            }
        }
        return $success;
    }

    public static function initializeRealtimeClient()
    {
        $user = request()->user();
        if (is_a($user, '\App\User')) {
            switch (config('app.rtu.method')) {
                case 'polling':
                    $url = URL::temporarySignedRoute('rtu', now()->addMinutes(1));
                    echo sprintf('<script type="text/javascript">runWhenTrue("(\'function\' == typeof(runRTU))", function() {runRTU(\'%s\');})</script>', $url);
                    break;
            }
        }
    }
}
