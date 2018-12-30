<?php
namespace   App\Services;

use App\Models\Order;
use Illuminate\Support\Carbon;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Models\CouponCode;
use App\Exceptions\CouponCodeUnavailableException;


class OrderService{
    public function store($user, $address, $remark, $items, $coupon = null)
    {

        // 判断如果优惠券有值 则先检测优惠券是否可用   ($coupon 如果有值就是coupon对象)
        if($coupon){
            $coupon->checkAvailable($user);
        }
        // 1 开启事务
        $order = \DB::transaction(function () use($user, $address, $remark, $items, $coupon){
            // 1 更新 user_address 的 last_used_at 字段
            $address->update(['last_used_at', Carbon::now()]);

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
                'total_amount' => 0   // 因为数据库设置了不能为空  所以暂且存 0 
            ]);
            // 3 订单关联到当前用户
            $order->user()->associate($user);
            $order->save();

            // 4 遍历 items  1->将订单中的单品信息存入orderitems表， 2-> 获取总金额
            $total = 0;
            // items  是个二维数组
            foreach($items as $item){   // item 只有 price 和 amount 和 sku_id
                $productSku = ProductSku::findOrFail($item['sku_id']);
                // make 是什么意思
                $orderItem = $order->items()->make([
                    'price'=>$productSku->price,
                    'amount' => $item['amount'],
                ]);
                $orderItem->productSku()->associate($productSku);// 关联到sku
                $orderItem->product()->associate($productSku->product_id);// 关联到商品
                $orderItem->save();
                // product_sku 库存自减
                if($productSku->decreaseStock($item['amount']) <= 0){
                    throw new InvalidRequestException('该商品库存不足');
                }
                // 计算总金额
                $total += $productSku->price * $item['amount'];
            };
            // 获取到了金额， 检查金额是否符合最低消费
            if($coupon){
                $coupon->checkAvailable($user, $total);
                //修改金额
                $total = $coupon->getAdjustedPrice($total);
                // 将订单与优惠券关联起来
                $order->coupon()->associate($coupon);
                // 增加优惠券的用量
                if($coupon->changeUsed() <= 0){
                    throw new CouponCodeUnavailableException('抱歉，该优惠券已被兑换完');
                }
            }
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