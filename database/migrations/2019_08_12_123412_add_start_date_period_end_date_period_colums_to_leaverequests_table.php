<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStartDatePeriodEndDatePeriodColumsToLeaverequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leaverequests', function (Blueprint $table) {
            $table->string('start_date_period')->nullable();
            $table->string('end_date_period')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leaverequests', function (Blueprint $table) {
            //
        });
    }
}
