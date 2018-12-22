<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use Illuminate\Support\Carbon;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Models\Order;

class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        $user = $request->user();
        // DB::transaction() 方法会开启一个数据库事务，在回调函数里的所有 SQL 写操作都会被包含在这个事务里，如果回调函数抛出异常则会自动回滚这个事务，否则提交事务。用这个方法可以帮我们节省不少代码。
        $order = \DB::transaction(function () use ($user, $request) {
            // 获取地址
            $address = UserAddress::findOrFail($request->address_id);
            // 更新此地址的最后使用时间
            $address->update(['last_used_at'=>Carbon::now()]);
            // 创建订单
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

            // 订单关联到当前用户
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            $items = $request->items;

            // 遍历用户提交的 sku
            foreach($items as $data){
                $sku = ProductSku::findOrFail($data['sku_id']);
                // 创建一个OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price' => $sku->price
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }
            // 更新订单总金额
            $order->update(['total_amount' => $totalAmount]);

            // 将下单的商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id');
            $user->cart()->whereIn('product_sku_id', $skuIds)->delete();
            return $order;
        });
        return $order;
    }
}
