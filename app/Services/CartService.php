<?php
namespace   App\Services;

use App\Models\Cart;

class CartService{
    // 获得当前用户的购物车数据并且预加载所属的商品
    public function get()
    {
        return auth()->user()->Cart()->with(['productSku.product'])->get();
    }

    // 增
    public function store($skuId, $amount)
    {
        // 判断这个单品是否已经在当前用户的购物车
        if($cart = auth()->user()->cart()->where('product_sku_id', $skuId)->first()){
            // 如果存在，则只是叠加数量
            $cart->update([
                'amount' => $amount + $cart->amount
            ]);
        }else{
            // 否则创建新的购物车记录
            // associate()  只在 belongsTo 时有效
            $cart = new Cart(['amount' => $amount]);
            $cart->user()->associate(auth()->user());
            $cart->productSku()->associate($skuId);
            $cart->save();
        }
        return $cart;   // 返回购物车实例
    }

    public function destroy($skuIds)
    {
        // 可以传单个 ID，也可以传 ID 数组， 所以把他转换成数组 
        if (!is_array($skuIds)) {
            $skuIds = [$skuIds];
        }

        auth()->user()->cart()->whereIn('product_sku_id', $skuIds)->delete();
    }
}