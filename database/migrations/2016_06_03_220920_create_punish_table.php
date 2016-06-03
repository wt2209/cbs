<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePunishTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('punish', function (Blueprint $table) {
            $table->increments('punish_id');
            $table->integer('company_id');
            $table->integer('user_id');//开罚单人
            $table->decimal('money');
            $table->string('reason');
            $table->string('punish_remark');
            $table->timestamp('charged_at');//缴费时间
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
        //
    }
}
