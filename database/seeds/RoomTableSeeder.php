<?php

use Illuminate\Database\Seeder;

class RoomTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /*$table->increments('room_id');
        $table->integer('company_id', false, true)->default(0);
        $table->string('building', 3);
        $table->smallInteger('room_number', false, true)->default(0);
        $table->string('room_remark');
        $table->timestamps();*/
        for ($i = 1; $i <= 10; $i++) {
            DB::table('room')->insert([
                'company_id'=> $i,
                'building'=> $i,
                'room_number'=> '101',
                'room_remark'=> '公司备注'.str_random(10),
            ]);
        }
        DB::table('room')->insert([
            'company_id'=> 0,
            'building'=> 11,
            'room_number'=> '101',
            'room_remark'=> '空房间'.str_random(10),
        ]);
    }
}
