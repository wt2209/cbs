<?php

use Illuminate\Database\Seeder;

class RentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rent_type')->insert([
            'person_number'=> 6,
            'rent_money'=> 588,
        ]);
        DB::table('rent_type')->insert([
            'person_number'=> 8,
            'rent_money'=> 768,
        ]);
        DB::table('rent_type')->insert([
            'person_number'=> 12,
            'rent_money'=> 1128,
        ]);
    }
}
