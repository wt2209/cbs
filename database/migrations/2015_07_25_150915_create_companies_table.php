<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company', function (Blueprint $table) {
            $table->increments('company_id');
            $table->string('company_name', 60);
            $table->string('company_description');
            $table->string('linkman', 4);
            $table->string('linkman_tel', 12);
            $table->string('manager');
            $table->string('manager_tel', 12);
            $table->string('company_remark');
            //是否退租
            $table->tinyInteger('is_quit')->default('0');
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
        Schema::drop('company');
    }
}
