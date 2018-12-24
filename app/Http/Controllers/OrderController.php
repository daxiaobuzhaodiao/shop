<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use Illuminate\Support\Carbon;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use App\Jobs\CloseOrder;

class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        // dd($request->all());
        $user = $request->user();
        // DB::transaction() 方法会开启一个数据库事务，在回调函数里的所有 SQL 写操作都会被包含在这个事务里，如果回调函数抛出异常则会自动回滚这个事务
        $order = \DB::transaction(function () use ($user, $request) {
            // 1 更新地址的 last_used_at 字段
            $address = UserAddress::findOrFail($request->address_id);
            $address->update(['last_used_at'=>Carbon::now()]);

            // 2 创建订单   并不是支付订单 所以这里只更新order表的部分字段
            $order = new Order([
                'address' => [
                    'address' =>$address->getFullAddress(),
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'remark' => $request->remark,
                'total_amount' => 0,
            ]);

            // 3 当前的生成的订单关联到当前用户               associate 只适用于 belongsTo 模式
            $order->user()->associate($user);
            $order->save();// 不要忘记save（）方法保存

            
            // 4 计算总金额
            $totalAmount = 0;       // 初始化金额变量
            $items = $request->items;
            // dd($items, collect($items));
            
            // 遍历用户提交的 sku  
            /*
                    "items" => array:2 [
                        0 => array:2 [
                        "sku_id" => 12
                        "amount" => "1"
                        ]
                        1 => array:2 [
                        "sku_id" => 13
                        "amount" => "3"
                        ]
                    ]
            */
            foreach($items as $data){
                $sku = ProductSku::findOrFail($data['sku_id']);
                // 利用关系给oderItem表中添加数据    items（）方法在order模型中定义了
                $item = $order->items()->make([
                    'amount' => $data['amount'],        // 被遍历的sku数量
                    'price' => $sku->price              // 被遍历的sku的单价
                ]);
                $item->product()->associate($sku->product_id);  // 关联到商品表
                $item->productSku()->associate($sku);           // 关联到sku表
                $item->save();
                // 计算总金额
                $totalAmount += $sku->price * $data['amount'];
                // sku 库存自减 decreaseStock（）和 自增方法 addStock（） 被定义在 sku的模型中的
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中移除   collect($items) 将数组转换成 collection 然后使用 pluck（）方法 将sku_id的值 转换成一个新数组
            $skuIds = collect($items)->pluck('sku_id');
            // dd($skuIds);
            $user->cart()->whereIn('product_sku_id', $skuIds)->delete();
            return $order;
        });

        // 加入延时队列 如果在规定时间没有完成支付将自动关闭订单 并且将sku库存还原
        $this->dispatch(new CloseOrder($order, config('app.order_tt')));
        return $order;
    }
}
