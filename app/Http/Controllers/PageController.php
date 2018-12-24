<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TheSeer\Tokenizer\Exception;
use App\Notifications\EmailVerificationNotification;
use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{

    // 凡是没有邮箱验证过的用户都会跳到这个页面
    public function emailVerifyNotice(){
        return view('pages.emailVerifyNotice');
    }

    // 发送邮件
    public function send(){
        $user = auth()->user();
        if($user->email_verified){
            throw new InvalidRequestException('您已经验证过邮箱了~~~');
        }
        // 调用notify方法 发送邮件并返回邮件发送成功页面
        $user->notify(new EmailVerificationNotification()); // 注意：：通知仅仅是针对用户的 这个对象就是当前用户对象不需要传参
        return view('pages.success', ['msg'=>'邮件发送成功！现在就去验证吧~']);
    }

    // 用户点击了邮箱中的验证链接  开始验证
    public function verify(Request $request)
    {
        $user = $request->user();
        $email = $request->email;
        $token = $request->token;
        // 链接中的参数缺一不可
        if(!$email || !$token){
            throw new InvalidRequestException('无效的验证链接');
        }
        // 验证链接中的token是否和缓存中的token一致
        if($token !== Cache::get('verification_'.$email)){
            throw new InvalidRequestException('验证链接不合法或者已过期');
        }
        
        // 到这一步说明 验证成功， 删除缓存中的token
        Cache::forget('verification'.$email);
        // 将该用户的 email_verified 字段改为 true
        $user->update(['email_verified' => true]);
        // 跳转一个页面告诉用户验证成功
        return view('pages.success', ['msg'=>'邮箱验证成功']);
        
        
    }
    
}
