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
            return DB::table('permission_role')
                ->join('permissions', 'permission_role.permission_id', '=', 'permissions.id')
                ->where('permission_name', $permission)
                ->where('role_id', $roleId)
                ->count();
        }
    }
}
