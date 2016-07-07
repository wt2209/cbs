<?php

namespace App\Http\Middleware;
use Auth;
use Route;
use App\Model\Role;
use Closure;
use App\Model\Permission;
class MyAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            //TODO
            $currentActionName = Route::current()->getActionName();
            $user = $request->user();
            if ($user->isAdmin()) {
                return $next($request);
            }
            foreach ($user->roles as $role) {
                if ($role->hasPermission($currentActionName, $role->id)) {
                    return $next($request);
                } else {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['message'=>'你没有权限！','status'=>0]);
                    } else {
                        return response('你没有权限！', 401);
                    }
                }
            }
        }
        return redirect()->to('login');
    }
}
