<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

// 邮箱验证 这里加上 MustVerifyEmail
class User extends Authenticatable implements MustVerifyEmail
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

    // 关联收获地址表
    public function address(){
        return $this->hasMany('App\Models\UserAddress');
    }

    /*
    belongsToMany() 方法用于定义一个多对多的关联，第一个参数是关联的模型类名，第二个参数是中间表的表名。
    withTimestamps() 代表中间表带有时间戳字段。
    orderBy('user_favorite_products.created_at', 'desc') 代表默认的排序方式是根据中间表中数据的创建时间倒序排序。
    */
    public function favoriteProducts(){
        return $this->belongsToMany('App\Models\Product', 'user_favorite_products')
            ->withTimestamps()
            ->orderBy('user_favorite_products.created_at', 'desc');
    }

    // 关联购物车表
    public function cart(){
        return $this->hasMany('App\Models\Cart');
    }
}
