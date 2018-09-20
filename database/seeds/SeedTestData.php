<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Group;
use App\User;
use App\Helpers\PermissionsHelper;

class SeedTestData extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(DatabaseSeeder::class);
        $role = Role::findOrFail(1);
        // Make Roles
        $roles = [];
        $this->addSubLevel(4, $roles, 2);
        foreach ($roles as $key => $sub_roles) {
        	$this->addRole($key, $sub_roles, $role);
        }
        // Make Groups
        $models = ['User', 'Role', 'Group'];
		$permissions = ['list', 'add', 'view', 'edit', 'delete'];
        $admin_group_raw = [
        	'name' => 'Admins',
        	'ip_whitelist' => 'all',
        ];
        foreach ($models as $model) {
        	foreach ($permissions as $verb) {
        		$field = sprintf('can_%s_%s', strtolower($verb), strtolower($model));
        		$admin_group_raw[$field] = 'all';
        	}
        }
        $user_group_raw = [
        	'name' => 'Users',
        	'ip_whitelist' => '10.0.0.0/24' . "\n" . '10.0.0.1/24' . "\n" . '10.0.2.0/24',
        ];
        foreach ($models as $model) {
        	foreach ($permissions as $verb) {
        		$field = sprintf('can_%s_%s', strtolower($verb), strtolower($model));
        		$user_group_raw[$field] = (in_array($model, ['User', 'Role', 'Group'])) ? 'none' : 'own';
        	}
        }
        $admin_group = Group::create($admin_group_raw);
        $user_group = Group::create($user_group_raw);
        echo sprintf( 'Admin Group ID "%d"' . "\n", $admin_group->id);
        echo sprintf( 'User Group ID "%d"' . "\n", $user_group->id);
        // Make Users & Add Group Associations
        factory(App\User::class, 50)->create()->each(function ($u) {
        	$role = Role::where('id', '<>', 1)->inRandomOrder()->first();
	        $u->role()->associate($role)->save();
	        $admin_group = Group::where(['name' => 'Admins'])->first();
			$user_group = Group::where(['name' => 'Users'])->first();
	        $group = ($u->id < 10) ? $admin_group : $user_group;
	        $u->groups()->attach($group->id);
	    });
    }

    private function addSubLevel($count, &$return, $depth = 0)
    {
    	if ($depth > 0) {
    		while (count($return) < $count) {
    			$key = sprintf('L%d-%d', count($return) + 1, $depth);
	    		$return[$key] = [];
	    		$this->addSubLevel($count, $return[$key], $depth - 1);
	    	}
    	}
    }

    private function addRole($key, $roles, Role $parent_role, $previous_name = '')
    {
    	$name = $previous_name . '|' . $key;
    	$role = Role::create([
    		'name' => $name,
    		'role_id' => $parent_role->id,
    	]);
    	echo sprintf('Created Role named "%s"' . "\n", $name);
    	foreach ($roles as $sk => $sr) {
    		$this->addRole($sk, $sr, $role, $name);
    	}
    }
}
