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

class EmitEventToUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, RealtimeEvent $event)
    {
        $this->user = $user;
        $this->event = $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        AuthenticatedSessionController::emitToUserSessions($this->user, $this->event);
    }
}
