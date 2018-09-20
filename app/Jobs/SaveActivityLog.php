<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Model;
use App\Activity;

class SaveActivityLog implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;
    protected $action;
    protected $changes = [];
    protected $user;
    protected $ip;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Model $model, $action, array $changes = [], $user = null, $ip = null)
    {
        $this->model = $model;
        $this->action = $action;
        $this->changes = $changes;
        $this->user = $user;
        $this->ip = $ip;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $am = new Activity;
        $am->model_type = $this->model->getMorphClass();
        $am->model_id = $this->model->id;
        $am->action = $this->action;
        $am->changes = $this->changes;
        $am->ip = $this->ip;
        if (is_a($this->user, '\Illuminate\Database\Eloquent\Model')) {
            $am->user()->associate($this->user);
        }
        $am->save();
    }
}
