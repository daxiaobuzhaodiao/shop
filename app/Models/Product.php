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

    // 因为商品的 image 字段保存的是图片的相对于目录 storage/app/public/ 的路径
    // 需要转成绝对路径才能正常展示，我们可以给商品模型加一个访问器来输出绝对路径：
    // Laravel 的模型访问器会自动把下划线改为驼峰，所以 image_url 对应的就是 getImageUrlAttribute。
    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        // 软链接
        // \Storage::disk('public') 的参数 public 需要和我们在 config/admin.php 里面的 upload.disk 配置一致。
        return \Storage::disk('public')->url($this->attributes['image']);
    }
}
