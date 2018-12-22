<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'stock' //库存
    ];

    public function product(){
        return $this->belongsTo('App\Models\Product');
    }

    public function cart(){
        return $this->hasMany('App\Models\Cart');
        
    }

    //decreaseStock() 方法里我们用了 $this->newQuery() 方法来获取数据库的查询构造器，ORM 查询构造器的写操作只会返回 true 或者 false 代表 SQL 是否执行成功，
    //而数据库查询构造器的写操作则会返回影响的行数。
    public function decreaseStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('减库存不可小于0');
        }

        return $this->newQuery()->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    //addStock() 加库存的逻辑里面不需要像减库存那样判断了，但仍需通过 increment() 方法来保证操作的原子性。
    public function addStock($amount)
    {
        if ($amount < 0) {
            throw new InternalException('加库存不可小于0');
        }
        $this->increment('stock', $amount);
    }
}
