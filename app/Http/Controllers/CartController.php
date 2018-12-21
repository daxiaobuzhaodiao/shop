<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\Cart;

class CartController extends Controller
{
    public function add(AddCartRequest $request){
        // dd($request->sku_id);
        $user = auth()->user(); //当前用户
        $skuId = $request->sku_id;  // 这个单品的id
        $amount = $request->amount;    // 加入购物车的数量

        // 判断这个单品是否已经在当前用户的购物车
        if($cart = $user->cart()->where('sku_id', $skuId)->first()){
            // 如果存在，则只是叠加数量
            $cart->update([
                'amount' => $amount
            ]);
        }else{
            
            // $cart = new Cart(['amount' => $amount]);
            // $cart->user()->associate($user);
            // $cart->productSku()->associate($skuId);
            // $cart->save();

            // 否则将本单品添加到购物车
            $res = $user->cart()->create([
                'amount' => $amount,
                'sku_id' => $skuId,
            ]);
        }
        return [];
    }
}
