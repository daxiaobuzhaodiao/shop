<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TheSeer\Tokenizer\Exception;
use App\Notifications\EmailVerificationNotification;
use App\Exceptions\InvalidRequestException;


class PagesController extends Controller
{
    public function root(){
        return view('pages.root');
    }

    public function emailVerifyNotice(){
        return view('pages.emailVerifyNotice');
    }

    public function send(){
        $user = auth()->user();
        if($user->email_verified){
            throw new InvalidRequestException('您已经验证过邮箱了~~~');
        }
        // 调用notify方法 发送邮件并返回邮件发送成功页面
        $user->notify(new EmailVerificationNotification()); // 注意：：通知仅仅是针对用户的 这个对象就是当前用户对象不需要传参
        return view('pages.success', ['msg'=>'邮件发送成功！,现在就去验证吧~']);
    }
}
