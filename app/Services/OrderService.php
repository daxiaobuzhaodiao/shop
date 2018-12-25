<?php
namespace   App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;

class OrderService{
    public function store($user, $address, $remark, $items)
    {
        // 1 开启事务
        $order = DB::transaction(function () use($user, $address, $remark, $items){
            // 1 更新 user_address 的 last_used_at 字段
            $address->update('last_used_at', Carbon::now());

            // 2 创建订单
            $order = new Order([
                // 将收货地址存入数据库， 虽然是数组格式， 存入数据库是以json格式存的
                'address'=>[
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone
                ],
                'remark' => $remark,
                'total_mark' => 0   //暂且存 0
            ]);

            // 3 订单关联到当前用户
            $order->user()->associate($user);
            $order->save();

            // 4 遍历 items  1->将订单中的单品信息存入orderitems表， 2-> 获取总金额
            $total = 0;
            foreach($items as $item){   // item 只有 price 和 amount 和 sku_id
                $productSku = ProductSku::findOrFail($item->sku_id);
                // 利用关系直接存储当前被遍历的sku信息
                $orderItem = $order->items()->create([
                    'price'=>$productSku->price,
                    'amount' => $item['amount']
                ]);
                $orderItem->user()->associate($user);// 关联到当前user
                $orderItem->product()->associate($productSku->product_id);// 关联到商品
                $orderItem->save();
                // product_sku 库存自减
                if($productSku->decreaseStock($item['amount']) <= 0){
                    throw new InvalidRequestException('该商品库存不足');
                }
                // 计算总金额
                $total += $productSku->price * $item['amount'];
            };
            // 5 更新总金额
            $order->update(['total_amount'=>$total]);
            // 6 将下单的产品从购物车中移除 （CartService）
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->destroy($skuIds);
            return $order;
        });
        // 规定时间内未支付则关闭此订单  还原库存
        dispatch(new CloseOrder($order, config('app.order_tt')));
        return $order;
        // 在控制器中可以通过 $this->dispatch() 方法来触发任务类，但在我们的封装的类中并没有这个方法，因此关闭订单的任务类改为 dispatch() 辅助函数来触发
    }
}