<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUtilityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        room_id company_id(is_quit) water_money electric_money year month utility_remark
        Schema::create('utility', function (Blueprint $table) {
            $table->increments('utility_id');
            $table->integer('room_id', false, true);
            $table->integer('company_id', false, true);
            $table->decimal('water_money');
            $table->decimal('electric_money');
            $table->smallInteger('year', false, true);
            $table->tinyInteger('month', false, true);
            $table->tinyInteger('is_charged', false, true)->default(0);
            $table->timestamp('charge_time');
            $table->string('utility_remark');
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
        Schema::drop('utility');
    }
}
