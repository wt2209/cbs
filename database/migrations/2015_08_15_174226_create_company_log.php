<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_log', function (Blueprint $table) {
            $table->increments('cl_id');
            $table->smallInteger('user_id',false, true);
            //房间变动类型： 1：增加房间， 2：减少房间， 3：人数变动， 4：性别变动， 5：性别和人数变动
            $table->tinyInteger('room_change_type');
            $table->integer('room_id');
            $table->integer('company_id');
            $table->tinyInteger('pre_rent_type');
            $table->tinyInteger('new_rent_type');
            $table->tinyInteger('pre_gender');
            $table->tinyInteger('new_gender');
            $table->integer('electric_base');
            $table->integer('water_base');
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
        Schema::drop('company_log');
    }
}
