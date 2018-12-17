<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password','email_verified'
    ];

    // 因为 email_verified 字段是个boolean类型 所以告诉laravel转换成 boolean 值
    protected $casts = [
        'email_verified'=>'boolean'
    ];
   
    protected $hidden = [
        'password', 'remember_token',
    ];
}
