<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'province',
        'city',
        'district',
        'address',
        'zip',
        'contact_name',
        'contact_phone',
        'last_used_at'
    ];
    
    // carbon时间日期对象
    protected $dates = ['last_used_at'];

    // 一对多关系  在user表中也需要定义  hasMany
    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    // 创建了一个访问器，在之后的代码里可以直接通过 $address->full_address 来获取完整的地址，而不用每次都去拼接。
    public function getFullAddress(){
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}
