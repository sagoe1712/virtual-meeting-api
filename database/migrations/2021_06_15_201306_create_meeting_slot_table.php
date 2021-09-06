<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingSlotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_slot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meeting_id')->nullable();
            $table->foreign('meeting_id')->references('id')->on('meeting_details');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('user');
            $table->dateTime('appointment_start');
            $table->dateTime('appointment_end');
            $table->bigInteger('status');
            $table->foreign('status')->references('id')->on('appointment_status');
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
        Schema::table('meeting_slot', function (Blueprint $table) {
            $table->dropForeign('status'); 
            $table->dropForeign('meeting_id'); 
            $table->dropForeign('user_id'); 
            });
        Schema::dropIfExists('meeting_slot');
    }
}
