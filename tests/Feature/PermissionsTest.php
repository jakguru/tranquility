<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Helpers\PermissionsHelper;

class PermissionsTest extends TestCase
{
    use WithFaker;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPermissions()
    {
        $user = User::findOrFail(1);
        $this->assertTrue($user->isSudo());
        $models = ['User', 'Role', 'Group'];
        $permissions = ['list', 'add', 'view', 'edit', 'delete'];
        foreach ($models as $model) {
            foreach ($permissions as $verb) {
                $this->assertEquals('all', $user->getPermissionForVerb($model, $verb));
                $this->assertEquals('none', $user->getPermissionForVerb($model, $verb, false));
            }
        }
    }

    public function testOwnership()
    {
        $user = User::findOrFail(1);
        $ids = $user->getOwnerIds();
        $this->assertEquals(51, count($ids));
    }

    public function testModularPermissionPolicy()
    {
        $sudo_user = User::findOrFail(1);
        $activity = \App\Activity::all()->first();
        $permissions = ['view', 'edit', 'delete'];
        $this->assertTrue($sudo_user->can('list', new \App\Activity), 'Sudo User cannot list an Activity');
        $this->assertTrue($sudo_user->can('add', new \App\Activity), 'Sudo User cannot add an Activity');
        foreach ($permissions as $p) {
            $this->assertTrue($sudo_user->can($p, $activity), sprintf('Sudo User cannot %s an Activity', $p));
        }
        $groups = \App\Group::where('id', '<>', 1)->get();
        foreach ($groups as $group) {
            $group_user = $group->users()->first();
            $this->assertFalse($group_user->can('list', new \App\Activity), sprintf('Non-Sudo User %d can list an Activity', $group_user->id));
            $this->assertFalse($group_user->can('add', new \App\Activity), sprintf('Non-Sudo User %d can add an Activity', $group_user->id));
            foreach ($permissions as $p) {
                $this->assertFalse($group_user->can($p, $activity), sprintf('Non-Sudo User %d can %s an Activity', $group_user->id, $p));
            }
        }
    }

    public function testIPRestrictions()
    {
        $sudo_user = User::findOrFail(1);
        $faker = $this->faker();
        $external_ipv4 = [];
        while (count($external_ipv4) < 50) {
            array_push($external_ipv4, $faker->ipv4());
        }
        $external_ipv6 = [];
        while (count($external_ipv6) < 50) {
            array_push($external_ipv6, $faker->ipv6());
        }
        $local_ipv4 = [];
        while (count($local_ipv4) < 50) {
            array_push($local_ipv4, $faker->localIpv4());
        }
        $groups = \App\Group::where('id', '<>', 1)->get();
        foreach ($groups as $group) {
            $group_user = $group->users()->first();
            foreach ($external_ipv4 as $ip) {
                $allowed = $group_user->canUseIp($ip, true);
                if (2 == $group->id) {
                    $this->assertTrue($allowed, sprintf('Group %d is allowed to login from IP %s', $group->id, $ip));
                } else {
                    $this->assertFalse($allowed, sprintf('Group %d is allowed to login from IP %s', $group->id, $ip));
                }
            }
            foreach ($local_ipv4 as $ip) {
                $allowed = $group_user->canUseIp($ip, true);
                if (2 == $group->id) {
                    $this->assertTrue($allowed, sprintf('Group %d is allowed to login from IP %s', $group->id, $ip));
                } else {
                    $this->assertFalse($allowed, sprintf('Group %d is allowed to login from IP %s', $group->id, $ip));
                }
            }
            foreach ($external_ipv6 as $ip) {
                $allowed = $group_user->canUseIp($ip, true);
                if (2 == $group->id) {
                    $this->assertTrue($allowed, sprintf('Group %d is allowed to login from IP %s', $group->id, $ip));
                } else {
                    $this->assertFalse($allowed, sprintf('Group %d is allowed to login from IP %s', $group->id, $ip));
                }
            }
        }
    }
}
