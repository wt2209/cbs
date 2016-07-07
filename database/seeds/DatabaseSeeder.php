<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(CompanyTableSeeder::class);
        $this->call(RoomTableSeeder::class);
        $this->call(UtilityBaseTableSeeder::class);
        $this->call(RentTypeSeeder::class);
        $this->call(UtilityTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(RoleUserTableSeeder::class);

        Model::reguard();
    }
}
