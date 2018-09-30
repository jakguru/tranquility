<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Helpers\CountryHelper;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('salutation')->nullable();
            $table->string('fName');
            $table->string('lName');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('main_phone')->nullable();
            $table->enum('main_phone_country', array_keys(CountryHelper::$countries))->default('XX');
            $table->string('mobile_phone')->nullable();
            $table->enum('mobile_phone_country', array_keys(CountryHelper::$countries))->default('XX');
            $table->string('home_phone')->nullable();
            $table->enum('home_phone_country', array_keys(CountryHelper::$countries))->default('XX');
            $table->string('work_phone')->nullable();
            $table->enum('work_phone_country', array_keys(CountryHelper::$countries))->default('XX');
            $table->string('fax_phone')->nullable();
            $table->enum('fax_phone_country', array_keys(CountryHelper::$countries))->default('XX');
            $table->string('password');
            $table->integer('role_id')->unsigned()->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('locked')->default(false);
            $table->json('owner_ids')->nullable();
            $table->json('peer_ids')->nullable();
            $table->json('parent_ids')->nullable();
            $table->text('google2fa_secret')->nullable();
            $table->ipAddress('last_login_ip')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal')->nullable();
            $table->enum('country', array_keys(CountryHelper::$countries))->default('XX');
            $table->string('avatar')->nullable();
            $table->string('title')->nullable();
            $table->string('timezone')->default('UTC');
            $table->string('whatsapp_phone')->nullable();
            $table->enum('whatsapp_phone_country', array_keys(CountryHelper::$countries))->default('XX');
            $table->string('telegram_phone')->nullable();
            $table->enum('telegram_phone_country', array_keys(CountryHelper::$countries))->default('XX');
            $table->string('viber_phone')->nullable();
            $table->enum('viber_phone_country', array_keys(CountryHelper::$countries))->default('XX');
            $table->string('skype')->nullable();
            $table->string('facebook')->nullable();
            $table->string('googleplus')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('temperature_unit')->default('celsius');
            $table->string('dateformat')->nullable();
            $table->string('timeformat')->nullable();
            $table->string('datetimeformat')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
