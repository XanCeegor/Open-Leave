<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeaverequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leaverequests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('leavenr');
            $table->string('requested_by');
            $table->string('username');
            $table->string('duration');
            $table->string('type');
            $table->string('reason')->nullable();
            $table->string('comment')->nullable();
            $table->string('status');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('start_date_radio');
            $table->string('end_date_radio');
            $table->string('department')->nullable();
            $table->string('public_holidays')->nullable();
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
        Schema::dropIfExists('leaverequests');
    }
}
