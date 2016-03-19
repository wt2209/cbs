<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFireguidersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fireguider', function (Blueprint $table) {
            $table->increments('fireguider_id');
            $table->string('fireguider_name', 4);
            $table->string('fireguider_tel', 12);
            $table->integer('fireguider_company_id', false, true);
            $table->string('fireguider_building', 3);
            $table->tinyInteger('fireguider_floor', false, true);
            $table->smallInteger('fireguider_room_number', false, true);
            $table->string('fireguider_remark');
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
        Schema::drop('fireguider');
    }
}
