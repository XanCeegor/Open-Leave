<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->string('name');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('entitled_days')->default(0);  //1.25 days per month for 15 days, 1.75 for 21 days
            $table->string('days_taken')->default(0);
            $table->boolean('extra_days')->default(false);  //user has 20 days if this is true, else 15 days
            $table->boolean('can_carry_over')->default(false);  //user may carry over leave from previous year
            $table->string('sick_days')->default(30);  //30 days in 3 year period
            $table->string('family_days')->default(3); //3 per year
            $table->string('unpaid_leave')->default(0);    //increment
            $table->string('parental_leave')->default(0);
            $table->string('employment_date')->nullable();    //date user started working. If year is same as current year, then compare from this date, else 1 Jan
            $table->string('email')->nullable();
            $table->string('department')->nullable();
            $table->string('country');
            $table->string('objectguid')->nullable();
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
