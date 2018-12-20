<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'title',    // 商品名称
        'description',  // 商品详情
        'image',    // 商品封面图片（路径）
        'on_sale',  // 是否上架
        'rating',   //商品平均评分
        'sold_count',   //销量
        'review_count', // 评价数量
        'price' //sku 最低价格
    ];

    protected $casts = [
        'on_sale' => 'boolean',
    ];

    // 关联sku表
    public function sku(){
        return $this->hasMany('App\Models\ProductSku');
    }

    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }
}
