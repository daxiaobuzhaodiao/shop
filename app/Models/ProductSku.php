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
}
