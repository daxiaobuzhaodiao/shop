<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfEmailVerified
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
        // 判断邮箱是否被认证
        if(!$request->user()->email_verified){
            // 如果是ajax请求则返回json字符串 
            if($request->expectsJson()){
                return response()->json(['msg' => '请验证邮箱后再来~'], 400);
            }
            // 重定向到路由
            return redirect()->route('email_verify_notice');
        }
        return $next($request);
    }
}
