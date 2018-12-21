<?php

namespace App\Http\Requests;

use App\Models\ProductSku;


class AddCartRequest extends Request
{
    public function rules()
    {
        return [
            'sku_id'=>[
                'required',
                function ($attribute, $value, $fail) {
                    if(!$sku = ProductSku::find($value)){   // 是否有这个单品存在
                        $fail('该商品不存在');
                        return;
                    }
                    if(!$sku->product->on_sale){  // 调用关系查看商品是否上架
                        $fail('商品未上架');
                        return;
                    }
                    if($sku->stock === 0){      // 检查该单品是否有库存
                        $fail('该商品已售完');
                        return;
                    }
                    if($this->amount > 0 && $sku->stock < $this->amount){  // 该商品目前的库存数量不能小于用户需求的数量
                        $fail('该商品库存不足');
                        return;
                    }
                }
            ],
            'amount'=>'required|integer|min:1'
        ];
    }

    public function attributes()
    {
        return [
            'amount'=>'商品数量'
        ]; 
    }

    public function messages()
    {
        return [
            'sku_id.required' => '请选择商品'
        ];
    }
}
