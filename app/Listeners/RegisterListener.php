<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Auth\Events\Registered;
use App\Notifications\EmailVerificationNotification;

class RegisterListener implements ShouldQueue
{

    public function __construct()
    {
        //
    }

    
    // 用户注册成功后， 触发这个方法， 继续调用 EmailVerificationNotification 这个通知类给用户发验证邮件
    public function handle(Registered $event)
    {
        $user = $event->user;
        
        // new EmailVerificationNotification($user);
        $user->notify(new EmailVerificationNotification());
    }
}
