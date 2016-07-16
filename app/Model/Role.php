<?php

namespace App\Model;
use DB;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    public function hasPermission($permission, $roleId)
    {
        if (is_string($permission)) {
            //不在permission表中的方法，直接放行
            if (DB::table('permission')
                ->where('permission_name', '=', $permission)
            ->count() === 0)
            {
                return true;
            }
            //查找角色是否具有权限
            return DB::table('permission_role')
                ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
                ->where('permission_name', $permission)
                ->where('role_id', $roleId)
                ->count();
        }
        return false;
    }
}
