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
            $table->integer('cancel_user_id');//撤销人
            $table->decimal('money');
            $table->string('reason');
            $table->string('cancel_reason');//撤销原因
            $table->string('punish_remark');
            $table->tinyInteger('is_charged')->default(0);
            $table->tinyInteger('is_canceled')->default(0);
            $table->timestamp('charged_at');//缴费时间
            $table->timestamp('cancel_at');//撤销时间
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
        Schema::drop('punish');
    }
}
