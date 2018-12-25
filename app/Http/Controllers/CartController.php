<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\Cart;
use App\Models\ProductSku;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    // 购物车列表
    public function index(){
        $addresses = auth()->user()->address()->orderBy('last_used_at', 'desc')->get();
        $cartItems = $this->cartService->get();
        return view('carts.index', compact('cartItems', 'addresses'));
    }

    // 增
    public function store(AddCartRequest $request){
        // 购物车表的字段只需要 sku_id 和 数量就行了
        $this->cartService->store($request->sku_id, $request->amount);
        return [];
    }

    // 删
    public function destroy($productSku)
    {
       
        $this->cartService->destroy($productSku);
        return [];

        // 这个方法报错     No query results for model [App\Models\Cart] 7
        // $cart = Cart::findOrFail($productSku);
        // $cart->delete($skuId);
    }

   
}
