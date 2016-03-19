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
            //TODO 还没有建立用户id，使用的是smallint
            $table->smallInteger('user_id',false, true);
            $table->string('company_name', 60);
            //操作类型，1|入住 2|调整房间 3|退房 4|删除
            $table->tinyInteger('type', false, true);
            $table->text('old_rooms');
            $table->text('new_rooms');
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
