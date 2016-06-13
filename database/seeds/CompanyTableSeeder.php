<?php

use Illuminate\Database\Seeder;

class CompanyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <= 16; $i++) {
            DB::table('company')->insert([
                'company_name'=> '公司'.random_int(1, 10),
                'company_description'=> '我是房间说明'.random_int(1, 10),
                'linkman'=> str_random(3),
                'linkman_tel'=> 15236654125,
                'manager'=> str_random(3),
                'manager_tel'=> 15236654125,
                'company_remark'=> '公司备注'.str_random(10),
                'created_at'=>date('Y-m-d')
            ]);
        }
    }
}
