<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Model\Role;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUsers()
    {
        $users = User::get();
        return view('user.users', ['users'=>$users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRoles()
    {
        $roles = Role::get();
        return view('user.roles', ['roles'=>$roles]);
    }


    /**
     * 添加角色
     */
    public function getCreateRole()
    {

    }

    /**
     * 修改角色的权限
     * @param $roleId
     */
    public function getEditRolePermission($roleId)
    {
        $roleId = (int) $roleId;
        $roleName = Role::where('id', '=', $roleId)->value('role_name');

        $tmpArr = DB::table('permission_role')->where('role_id', '=', $roleId)->get();
        $rolePermissionIds = [];
        foreach ($tmpArr as $tmp) {
            $rolePermissionIds[] = $tmp->permission_id;
        }

        $allPermissions = DB::table('permissions')->get();

        return view('user.editRolePermission', [
            'roleName'=>$roleName,
            'allPermissions'=>$allPermissions,
            'rolePermissionIds'=>$rolePermissionIds
        ]);
    }

    public function getRemoveUser(Request $request)
    {
        $user = User::where('id','=',$request->delete_id)->first();
        if (!$user) {
            return response()->json(['message'=>'失败：非法请求！','status'=>0]);
        }
        foreach ($user->roles as $role) {
            if ($role->is_admin == 1) {
                return response()->json(['message'=>'失败：不能删除超级管理员！','status'=>0]);
            }
        }

        if (User::destroy($request->delete_id)) {
            return response()->json(['message'=>'操作成功！','status'=>1]);
        }
        return response()->json(['message'=>'失败：请重试！','status'=>0]);
    }

    public function getRemoveRole(Request $request)
    {

        if (Role::where('id','=',$request->delete_id)->where('is_admin', '=', 1)->count()>0) {
            return response()->json(['message'=>'失败：不能删除超级管理员！','status'=>0]);
        }
        if (DB::table('role_user')->where('role_id', '=',$request->delete_id)->count()>0) {
            return response()->json(['message'=>'失败：请先删除此角色下的用户！','status'=>0]);
        }

        if (Role::destroy($request->delete_id)) {
            return response()->json(['message'=>'操作成功！','status'=>1]);
        }
        return response()->json(['message'=>'失败：请重试！','status'=>0]);
    }

}
