<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponCode extends Model
{
    // 用常量的方式定义支持的优惠券类型
    const TYPE_FIXED = 'fixed'; // 表示固定金额
    const TYPE_PERCENT = 'percent'; // 表示比例

    public static $typeMap = [
        self::TYPE_FIXED   => '固定金额',
        self::TYPE_PERCENT => '比例',
    ];
    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled'
    ];
    
    protected $casts = [
        'enabled' => 'boolean',
    ];

    protected $dates = [
        'not_before',
        'not_after'
    ];

    // 生成优惠券的16位随机号码
    public static function findAvailableCode($length = 16)
    {
        do {
            // 生成一个指定长度的随机字符串，并转成大写
            $code = strtoupper(Str::random($length));
        // 如果生成的码已存在就继续循环
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }
   
    protected $appends = ['description'];  // 加一个虚拟字段 在后台优惠券列表中显示
    public function getDescriptionAttribute()
    {
        $str = '';

        if ($this->min_amount > 0) {
            $str = '满'.str_replace('.00', '', $this->min_amount);
        }
        if ($this->type === self::TYPE_PERCENT) {
            return $str.'优惠'.str_replace('.00', '', $this->value).'%';
        }

        return $str.'减'.str_replace('.00', '', $this->value);
    }
}
