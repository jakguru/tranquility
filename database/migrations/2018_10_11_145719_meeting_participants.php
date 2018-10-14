<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MeetingParticipants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meetings', function($table) {
            $table->json('email_participants')->nullable();
        });
        Schema::create('participants', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('meeting_id')->unsigned(); 
        	$table->string('participant_type');
            $table->integer('participant_id')->unsigned();
            $table->enum('status', ['pending', 'accepted', 'rejected']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function($table) {
            $table->dropColumn('email_participants');
        });
        Schema::dropIfExists('participants');
    }
}
