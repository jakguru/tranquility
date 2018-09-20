<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('locked')->default(false);
            $table->integer('role_id')->unsigned()->nullable();
            $table->foreign('role_id')->references('id')->on('roles')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->timestamps();
        });
        Schema::table('users', function($table) {
            $table->foreign('role_id')->references('id')->on('roles')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function($table) {
            $table->dropForeign('roles_role_id_foreign');
        });
        Schema::table('users', function($table) {
            $table->dropForeign('users_role_id_foreign');
        });
        Schema::dropIfExists('roles');
    }
}
