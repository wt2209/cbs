<?php

use Illuminate\Database\Seeder;
use App\Model\Permission;
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $arr = [
            ['permission_name'=>'App\Http\Controllers\IndexController@getIndex', 'display_name'=>'系统首页'],
            ['permission_name'=>'App\Http\Controllers\CompanyController@getIndex', 'display_name'=>'公司首页'],
            ['permission_name'=>'App\Http\Controllers\CompanyController@getAdd', 'display_name'=>'公司入住'],
            ['permission_name'=>'App\Http\Controllers\CompanyController@getEdit', 'display_name'=>'修改公司'],
            ['permission_name'=>'App\Http\Controllers\CompanyController@getSelectRooms', 'display_name'=>'选择房间'],
            ['permission_name'=>'App\Http\Controllers\CompanyController@getChangeRooms', 'display_name'=>'调整房间'],
            ['permission_name'=>'App\Http\Controllers\CompanyController@getCompanyDetail', 'display_name'=>'公司明细'],
            ['permission_name'=>'App\Http\Controllers\CompanyController@getCompanyUtility', 'display_name'=>'公司水电'],
            ['permission_name'=>'App\Http\Controllers\CompanyController@getQuit', 'display_name'=>'公司退租'],
            ['permission_name'=>'App\Http\Controllers\CompanyLogController@getIndex', 'display_name'=>'公司变动记录'],
            ['permission_name'=>'App\Http\Controllers\PunishController@getChargedList', 'display_name'=>'未缴费罚单'],
            ['permission_name'=>'App\Http\Controllers\PunishController@getUnchargedList', 'display_name'=>'已缴费罚单'],
            ['permission_name'=>'App\Http\Controllers\PunishController@getCanceledList', 'display_name'=>'已取消罚单'],
            ['permission_name'=>'App\Http\Controllers\PunishController@getCharge', 'display_name'=>'罚单缴费'],
            ['permission_name'=>'App\Http\Controllers\PunishController@getCreate', 'display_name'=>'开罚单'],
            ['permission_name'=>'App\Http\Controllers\PunishController@getEditRemark', 'display_name'=>'修改罚单备注'],
            ['permission_name'=>'App\Http\Controllers\PunishController@getCancel', 'display_name'=>'取消罚单'],
            ['permission_name'=>'App\Http\Controllers\RoomController@getLivingRoom', 'display_name'=>'居住用房'],
            ['permission_name'=>'App\Http\Controllers\RoomController@getDiningRoom', 'display_name'=>'餐厅用房'],
            ['permission_name'=>'App\Http\Controllers\RoomController@getServiceRoom', 'display_name'=>'服务用房'],
            ['permission_name'=>'App\Http\Controllers\RoomController@getEdit', 'display_name'=>'修改房间'],
            ['permission_name'=>'App\Http\Controllers\UtilityController@getIndex', 'display_name'=>'水电费明细'],
            ['permission_name'=>'App\Http\Controllers\UtilityController@getBase', 'display_name'=>'水电表数'],
            ['permission_name'=>'App\Http\Controllers\UtilityController@getAdd', 'display_name'=>'录入底数'],
            ['permission_name'=>'App\Http\Controllers\UtilityController@getImportBaseFromFile', 'display_name'=>'导入底数'],
            ['permission_name'=>'App\Http\Controllers\UtilityController@getEdit', 'display_name'=>'修改水电费'],
            ['permission_name'=>'App\Http\Controllers\UtilityController@getEditBase', 'display_name'=>'修改底数'],
            ['permission_name'=>'App\Http\Controllers\UtilityController@getChargeSingleRoom', 'display_name'=>'房间缴费'],
            ['permission_name'=>'App\Http\Controllers\UtilityController@getDelete', 'display_name'=>'删除水电费'],
            ['permission_name'=>'App\Http\Controllers\UtilityController@getBaseDelete', 'display_name'=>'删除底数'],
            ['permission_name'=>'App\Http\Controllers\UserController@getUsers', 'display_name'=>'用户明细'],
            ['permission_name'=>'App\Http\Controllers\UserController@getRoles', 'display_name'=>'角色明细'],
            ['permission_name'=>'App\Http\Controllers\UserController@getChangePassword', 'display_name'=>'修改密码'],
            ['permission_name'=>'App\Http\Controllers\UserController@getCreateRole', 'display_name'=>'创建角色'],
            ['permission_name'=>'App\Http\Controllers\UserController@getEditRolePermission', 'display_name'=>'修改权限'],
            ['permission_name'=>'App\Http\Controllers\UserController@getRemoveUser', 'display_name'=>'删除用户'],
            ['permission_name'=>'App\Http\Controllers\UserController@getRemoveRole', 'display_name'=>'删除角色'],
        ];

        foreach ($arr as $a) {
            $a['created_at'] =  date('Y-m-d H:i:s');
            $a['updated_at'] =  date('Y-m-d H:i:s');
            Permission::insert($a);
        }
    }
}
