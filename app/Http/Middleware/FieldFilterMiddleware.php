<?php

namespace App\Http\Middleware;

use Closure;

class FieldFilterMiddleware
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
        //字段过滤
        foreach ($request->all() as $k=>$r) {
            //使用四个函数进行过滤，只对字符串进行处理
            if (is_string($r)) {
                $r = addslashes(htmlspecialchars(strip_tags(trim($r))));
                $request->offsetSet($k, $r);
            }
        }

        return $next($request);
    }
}
