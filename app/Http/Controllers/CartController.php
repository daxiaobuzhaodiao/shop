<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\Cart;
use App\Models\ProductSku;

class CartController extends Controller
{
    // 购物车列表
    public function index(){
        $addresses = auth()->user()->address()->orderBy('last_used_at', 'desc')->get();
        $cartItems = auth()->user()->cart()->with(['productSku.product'])->get();
        return view('carts.index', compact('cartItems', 'addresses'));
    }

    // 添加购物车
    public function store(AddCartRequest $request){
        $user = auth()->user(); 
        $skuId = $request->sku_id;  
        $amount = $request->amount;    

        // 判断这个单品是否已经在当前用户的购物车
        if($cart = $user->cart()->where('product_sku_id', $skuId)->first()){
            // 如果存在，则只是叠加数量
            $cart->update([
                'amount' => $amount+$cart->amount
            ]);
        }else{
            $cart = new Cart(['amount' => $amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }
        return [];
    }

    // 删除购物车
    public function destroy($productSku)
    {
        $res = auth()->user()->cart()->where('product_sku_id', $productSku)->delete();
        dd($res);
    }

   
}
