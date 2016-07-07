<?php

use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i<=10; $i++) {
            DB::table('permission_role')->insert([
                'permission_id'=>$i,
                'role_id'=>2
            ]);
        }
    }
}
