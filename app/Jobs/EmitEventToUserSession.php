<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use \App\User;
use \App\Realtime\RealtimeEvent;
use \App\AuthenticatedSession;
use \App\Http\Controllers\AuthenticatedSessionController;

class EmitEventToUserSession implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $event;
    protected $session;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, RealtimeEvent $event, AuthenticatedSession $session)
    {
        $this->user = $user;
        $this->event = $event;
        $this->session = $session;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        AuthenticatedSessionController::emitToUserSession($this->user, $this->event, $this->session);
    }
}
