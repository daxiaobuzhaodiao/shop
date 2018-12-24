<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EmailVerificationNotification extends Notification implements ShouldQueue  // 队列接口
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // 通过邮件通知
    public function via($notifiable)
    {
        return ['mail'];
    }

    // 只要new了这个类就会触发此方法 发送邮件时会调用此方法来发送邮件， 参数就是当前User实例
    public function toMail($notifiable)
    {
        // 使用laravel内置的方法生成随机字符串
        $token = Str::random(16);
        // 将随机字符串存入缓存
        Cache::put('verification_'.$notifiable->email, $token, 30); // 参数1 是key  参数2 是值 参数3 有效期
        // 制作验证链接请求地址
        $url = route('email_verification.verify', ['email'=>$notifiable->email, 'token'=>$token]);
        // 发送邮件， 并将验证链接发送给用户，让用户通过点击即链接实现验证邮箱
        return (new MailMessage)
            ->greeting($notifiable->name. '您好：')  //邮件的欢迎词
            ->subject('注册成功，请验证邮箱')   // 邮件标题
            ->line('点击下方链接开始验证，有效期30分钟')  // 一行内容
            ->action('验证', $url); // 激活链接
        }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
