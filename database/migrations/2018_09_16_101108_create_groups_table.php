<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Helpers\PermissionsHelper;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('sudo')->default(false);
            $table->boolean('infosec')->default(false);
            $table->boolean('locked')->default(false);
            /** User Model Permissions */
            PermissionsHelper::addPermissionsForModel('User', $table);
            /** Group Model Permissions */
            PermissionsHelper::addPermissionsForModel('Group', $table);
            /** Role Model Permissions */
            PermissionsHelper::addPermissionsForModel('Role', $table);
            $table->text('ip_whitelist')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
}
