<?php

use Illuminate\Database\Seeder;

class UtilityBaseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1-101 2-101 3-101....
        //id :1-10
        //year = 2015
        //month=1,2

        $arr = [];
        for ($i = 1; $i <= 11; $i++) {
            $arr[$i]['e'] = random_int(1, 99500);
            $arr[$i]['w'] = random_int(1, 9950);
            DB::table('utility_base')->insert([
                'room_id' => $i,
                'water_base' => $arr[$i]['w'],
                'electric_base' => $arr[$i]['e'],
                'year' => date('m') == 1 ? date('Y') - 1 : date('Y'),
                'month' => date('m') == 1 ? 12 : date('m') - 1,
                'recorder' => '张三',
                'record_time' => date('Y-m-d', strtotime('-1 month')),
                'u_base_remark' => '底数备注'
            ]);
        }
        for ($i = 1; $i <= 11; $i++) {
            DB::table('utility_base')->insert([
                'room_id'=> $i,
                'water_base'=> $arr[$i]['w'] + random_int(1, 49),
                'electric_base'=> $arr[$i]['e'] + random_int(1, 499),
                'year' => date('Y'),
                'month' => date('m'),
                'recorder'=>'张三',
                'record_time'=>date('Y-m-d'),
                'u_base_remark'=>'底数备注'
            ]);
        }
    }
}
