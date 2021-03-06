<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Exceptions\InvalidRequestException;
use App\Models\OrderItem;

class ProductController extends Controller
{
    // 商品列表
    public function index(Request $request){
        // 创建查询构造器
        // $builder = Product::query()->where('on_sale', true); 
        $builder = Product::where('on_sale', true);    // 有没有query() 都一样的
        // 判断用户是否使用了搜索(如果没有则略过代码块，如果有则赋值给变量$search)
        if($search = $request->input('search', '')){
            $like = '%'.$search.'%';
            // 模糊搜索商品标题、商品详情、SKU 标题、SKU描述
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('sku', function ($query) use ($like) { // 第一个参数就是  Product模型中关联PRoductSku模型的方法名
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
            // 优化搜索体验 搜索后搜索框不会变空白 前端页面通过js var filters =  {!! json_encode($filters) !!}
            'filters'=>[
                'search' => $search,
                'order' => $order
            ]
        ]);
    }

    // 商品详情页
    public function show(Product $product, Request $request)
    {
        // 1 判断商品是否已上架
        if(!$product->on_sale){
            throw new InvalidRequestException('该商品未上架');
        }
        // 2 收藏按钮的显示
        $favored = false;  // 这里设置默认false只是为了给访客看的 如果是访客的话 页面会报错找不到 $favored
        if($user = auth()->user()){
            $res = $user->favoriteProducts()->find($product->id);  //如果找到就返回这个商品对象！如果找不到返回null  null转换为boolean为false
            $favored = boolval($res);
        }
        // 3 加载该商品的评价内容  因为评价的是一个sku  
        $reviews = OrderItem::query()
            ->with(['order.user', 'productSku'])
            ->where('product_id', $product->id)     // 筛选只属于当前商品的sku
            ->whereNotNull('reviewed_at')       // 已经评价过的
            ->orderBy('reviewed_at', 'desc')    // 评价时间倒序排序
            ->limit(10)
            ->get();
        
            
        return view('products.show',compact('product', 'favored', 'reviews'));
    }

    // 收藏商品
    public function favorite(Product $product){
        $user = auth()->user();
        
        if($user->favoriteProducts()->find($product->id)) {
            // 用户已经收藏过了  也不用提醒直接返回空就行
            return [];
        }
        //attach() 方法将当前用户和此商品关联起来 参数可以是模型的 id，也可以是模型对象本身
        $user->favoriteProducts()->attach($product);
        return [];
    }

    // 取消收藏
    public function disfavorite(Product $product){
        $user = auth()->user();
        $user->favoriteProducts()->detach($product);  // 此方法和上方的attach方法类似取消用户和商品的关联
        return [];
    }

    // 返回我的收藏列表
    public function favorites(){
        // 在User模型中关联了收藏的表时已经设置过排序
        $products = auth()->user()->favoriteProducts()->paginate(8);
        return view('products.favorites', compact('products'));
    }
}
