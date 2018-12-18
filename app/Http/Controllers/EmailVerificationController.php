<?php

namespace App\Http\Controllers;

use App\Models\User;
use Cache;
use Illuminate\Http\Request;
use TheSeer\Tokenizer\Exception;

class EmailVerificationController extends Controller
{
    public function verify(Request $request){
        // 从url中获取email和token这两个参数
        $email = $request->email;
        $token = $request->token;
        // 如果有一个为空 则直接抛出异常
        if(!$email || !$token){
            throw new Exception('验证链接有误！！');
        }
        // 将缓存中的token和url中的token做对比 如果不匹配，抛出异常
        if($token != Cache::get('verification_'.$email)){
            throw new Exception('验证链接有误或者已过期');
        }
        // 通常来说通过验证的用户 在数据库中是存在的，但是为了代码健壮性进行以下判断
        if(auth()->user() != User::where('email', $email)->first()){
            throw new Exception('用户不存在');
        }
        // 完成验证，将缓存中的token删除掉
        Cache::forget('verification_'.$email);
        // 将该用户的email_verified修改为true
        auth()->user()->update(['email_verified'=>true]);
        // 通知用户邮箱验证完成
        return view('pages.success', ['msg'=>'邮箱验证成功']);

    }
}
