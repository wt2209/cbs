<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtilityBaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utility_base', function (Blueprint $table) {
            $table->increments('u_base_id');
            $table->integer('room_id');
            $table->integer('water_base');
            $table->integer('electric_base');
            $table->smallInteger('year');
            $table->tinyInteger('month');
            $table->string('recorder');
            $table->timestamp('record_time');
            $table->string('u_base_remark');
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
        Schema::drop('utility_base');
    }
}
