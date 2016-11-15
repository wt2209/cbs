<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\User;
use App\Model\Role;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('my.auth');
    }
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

    public function getChangePassword(Request $request)
    {
        $user = $request->user();
        return view('user.changePassword', ['user'=>$user]);
    }

    public function postChangePassword(Request $request)
    {
        if ($request->new_password !== $request->confirm_password) {
            return response()->json(['message'=>'错误：两次密码不一致！', 'status'=>0]);
        }
        $user = User::where('id', $request->user_id)->first();
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message'=>'错误：密码错误！', 'status'=>0]);
        }

        User::where('id', $request->user_id)->update(
            ['password' => Hash::make($request->new_password)]
        );
        return response()->json(['message'=>'操作成功！', 'status'=>1]);
    }
    /**
     * 添加角色
     */
    public function getCreateRole()
    {
        return view('user.createRole');
    }

    public function postCreateRole(Request $request)
    {
        if (empty($request->role_name)) {
            return back()->withErrors(['createRoleFailed'=>'角色名不能为空！']);
        }
        if (Role::where('role_name', $request->role_name)->count() > 0) {
            return back()->withErrors(['createRoleFailed'=>'角色名重复！']);
        }
        $data = [
            'role_name'=>$request->role_name,
            'role_description'=>$request->role_description,
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s')
        ];
        $roleId = Role::insertGetId($data);
        return redirect()->action('UserController@getEditRolePermission', [$roleId]);
    }

    /**
     * 修改权限
     * @param $roleId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
            'roleId'=>$roleId,
            'roleName'=>$roleName,
            'allPermissions'=>$allPermissions,
            'rolePermissionIds'=>$rolePermissionIds
        ]);
    }

    public function postEditRolePermission(Request $request)
    {
        $roleId = (int)$request->role_id;
        //删除就有权限
        DB::table('permission_role')->where('role_id', '=', $roleId)->delete();
        $data = [];
        foreach ($request->permission_id as $permissionId) {
            $data[] = [
                'role_id'=>$roleId,
                'permission_id'=>intval($permissionId)
            ];
        }
        DB::table('permission_role')->insert($data);
        return response()->json(['message'=>'操作成功！','status'=>1]);
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
