<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateOwnedUserIdsForRole implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $role_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($role_id)
    {
        $this->role_id = $role_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $roles_to_update = [];
        self::getRelatedRoles($this->role_id, $roles_to_update);
        $roles_to_update = array_filter($roles_to_update, function ($var) {
            return !is_null($var);
        });
        $users = \App\User::whereIn('role_id', $roles_to_update)->get();
        foreach ($users as $user) {
            UpdateOwnedUserIds::dispatch($user);
        }
    }

    private static function getRelatedRoles($role_id, &$roles, $parent = false)
    {
        if (!is_null($role_id)) {
            $role_id = intval($role_id);
        }
        if (!in_array($role_id, $roles)) {
            array_push($roles, $role_id);
            if (!is_null($role_id)) {
                $role = \App\Role::find($role_id);
                $parent_id = $role->role_id;
                self::getRelatedRoles($parent_id, $roles, true);
            }
            if (false == $parent) {
                foreach (\App\Role::where(['role_id' => $role_id])->get() as $role) {
                    self::getRelatedRoles($role->id, $roles);
                }
            }
        }
        $roles = array_unique($roles);
        sort($roles, SORT_NUMERIC);
    }
}
