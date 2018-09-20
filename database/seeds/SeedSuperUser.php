<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Role;
use App\Group;

class SeedSuperUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$role = Role::findOrFail(1);
    	$group = Role::findOrFail(1);
        $user = User::create([
        	'fName' => 'Super',
			'lName' => 'User',
			'email' => 'sudo@localhost.local',
			'password' => Hash::make('admin'),
			'role_id' => $role->id,
			'active' => true,
			'locked' => true,
        ]);
        DB::table('group_user')->insert([
        	'group_id' => $group->id,
			'user_id' => $user->id,
        ]);
    }
}
