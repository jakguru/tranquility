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

class RemoveEventsFromQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $events;
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, array $events = [])
    {
        $this->user = $user;
        $this->events = $events;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->events as $event) {
            AuthenticatedSessionController::removeEventFromSessions($this->user, $event);
        }
    }
}
