<?php

use Illuminate\Database\Seeder;
use App\Role;

class SeedInitialRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
        	'name' => 'System Administrator',
			'locked' => true,
        ]);
    }
}
