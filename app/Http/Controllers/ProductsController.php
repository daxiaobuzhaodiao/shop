<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;

class ProductsController extends Controller
{
    public function index(Request $request){
        // 创建查询构造器
        $builder = Product::query()->where('on_sale', true);
        // $builder = Product::where('on_sale', true);
        // 判断用户是否使用了搜索(如果没有则略过代码块，如果有则赋值给变量$search)
        if($search = $request->input('search', '')){
            $like = '%'.$search.'%';
            // 模糊搜索商品标题、商品详情、SKU 标题、SKU描述
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('sku', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }
        // 判断是否有排序条件
        if($order = $request->input('order', '')){
            // 是否已_asc或者_desc结尾
            if(preg_match('/^(.+)_(asc|desc)$/', $order, $res)){
                // dd($res);  // array [0 => "rating_desc", 1 => "rating", 2 => "desc"]
                if(in_array($res[1], ['price', 'rating', 'sold_count'] )){
                    // 说明是合法的排序值
                    $res = $builder->orderBy($res[1], $res[2]);
                }
            }
        }
        
        $products = $builder->paginate(16);
        return view('products.index',[
            'products'=>$products,
            // 优化搜索体验 搜索后搜索框不会变空白
            'filters'=>[
                'search' => $search,
                'order' => $order
            ]
        ]);
    }

    public function show(Product $product, Request $request)
    {
        // 判断商品是否已上架
        if(!$product->on_sale){
            throw new InvalidRequestException('该商品未上架');
        }
        return view('products.show',compact('product'));
    }
}
