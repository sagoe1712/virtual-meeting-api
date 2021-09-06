<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meeting_details', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company_name');
            $table->string('subject');
            $table->unsignedBigInteger('meeting_channel_id')->nullable();
            $table->string('meeting_link')->nullable();
            $table->foreign('meeting_channel_id')->references('id')->on('meeting_channel');
            $table->unsignedTinyInteger('status')->default(0);
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
        Schema::table('meeting_details', function (Blueprint $table) {
        $table->dropForeign('meeting_channel_id'); //
        });
        Schema::dropIfExists('meeting_details');
    }
}
