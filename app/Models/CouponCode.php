<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Exceptions\CouponCodeUnavailableException;
use Illuminate\Support\Carbon;

class CouponCode extends Model
{
    // 用常量的方式定义支持的优惠券类型
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED   => '金额',
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
        // 如果生成的码已存在就继续生成
        } while (self::query()->where('code', $code)->exists());
        return $code;
    }
   
    // 加一个虚拟字段 在后台优惠券列表中显示
    protected $appends = ['description'];  
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

    // coupon 有效性验证
    // 这个 checkAvailable() 方法接受一个参数 $orderAmount 订单金额。
    // 只是检查其有效性时 不需要传参
    // 在下单时需要传入当前订单的总金额 检查该券是否符合规定的最低消费金额
    public function checkAvailable($orderAmount = null)
    {
        if(!$this->enabled){
            throw new CouponCodeUnavailableException('优惠券不存在');
        }
        if($this->total - $this->used <= 0){
            throw new CouponCodeUnavailableException('优惠券已经被兑换完');
        }
        if($this->not_before && $this->not_before->gt(Carbon::now())){
            throw new CouponCodeUnavailableException('该优惠券暂时不可用');
        }
        if($this->not_after && $this->not_after->lt(Carbon::now())){
            throw new CouponCodeUnavailableException('该优惠券已经过期');
        }
        if(!is_null($orderAmount) && $orderAmount < $this->min_amount){
            throw new CouponCodeUnavailableException('订单金额不满足该优惠券的最低金额');
        }
    }

    // 计算优惠后的金额  需要传入当前订单总金额
    public function getAdjustedPrice($orderAmount)
    {
        // 判断该券的类型  固定金额 / 折扣
        if($this->type === self::TYPE_FIXED){
            // 为了保证系统健壮性  需要保证订单定额最少也得  0.01 元
            return max(0.01, $orderAmount - $this->value);
        }else{
            return number_format($orderAmount * (100 - $this->value) / 100, 2, '.', '');
        }
    }
    // 如果使用了则 增加用量， 如果订单自动关闭了则减少用量
    public function changeUsed($increase = true)
    {
        // 传入 true 则增加用量，否则减少用量
        if($increase){
            // 检查当前用量是否已经超过总量
            return $this->newQuery()->where('id', $this->id)->where('used', '<', $this->total)->increment('used');// 返回
        }else{
            return $this->decrement('used');
        }
    }
    
}
