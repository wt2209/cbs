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
        for ($i = 1; $i <= 4; $i++) {
            for ($j = 1; $j <= 16; $j++) {
                for ($m = 1; $m <= 17; $m++) {
                    DB::table('room')->insert([
                        'company_id'=> $j,
                        'room_type'=> 1,
                        'room_name'=> $i*10000+$j*100+$m,
                        'rent_type_id'=>1,
                        'gender'=>random_int(1,2),
                        'room_remark'=> '房间备注'.str_random(10),
                        'created_at'=>date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
        for ($i = 1; $i <= 9; $i++) {
            DB::table('room')->insert([
                'company_id'=> $i,
                'room_type'=> 2,
                'room_name'=> '餐厅'.$i,
                'room_remark'=> '餐厅备注'.str_random(10),
                'created_at'=>date('Y-m-d H:i:s')
            ]);
        }
        for ($i = 1; $i <= 5; $i++) {
            DB::table('room')->insert([
                'company_id'=> $i,
                'room_type'=> 3,
                'room_name'=> '办公'.$i,
                'room_remark'=> '办公备注'.str_random(10),
                'created_at'=>date('Y-m-d H:i:s')
            ]);
        }
    }
}
