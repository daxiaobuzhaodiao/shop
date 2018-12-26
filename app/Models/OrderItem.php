<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['amount', 'price', 'rating', 'review', 'reviewed_at'];
    protected $dates = ['reviewed_at']; // 评价时间
    public $timestamps = false;

    // 关联到商品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 关联到sku
    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    // 关联到订单
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
