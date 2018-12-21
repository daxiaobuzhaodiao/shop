<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductSku;

class Cart extends Model
{
    protected $fillable = [
        'amount'
    ];

    // 不需要维护时间的两个字段  注意关键字是 public
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function productSku(){
        return $this->belongsTo(ProductSku::class);
    }
}
