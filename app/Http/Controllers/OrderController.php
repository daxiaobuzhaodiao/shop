<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use App\Services\OrderService;
use App\Exceptions\InvalidRequestException;

class OrderController extends Controller
{
    // 订单列表
    public function index(){
        $orders = Order::query()->with(['items.product', 'items.productSku'])
            ->where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        return view('order.index', ['orders' => $orders]);
    }
        
    // 增 
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $address = UserAddress::findOrFail($request->address_id);
        $order = $orderService->store($request->user(), $address, $request->remark, $request->items);
        return $order;
    }

    // 订单详情
    public function show(Order $order)
    {
        // 查看权限
        $this->authorize('own', $order);
        // with 预加载 ORM查询构造器上调用
        // load 延迟预加载 在模型对象上调用
        return view('order.show', ['order'=>$order->load(['items.productSku', 'items.product'])]);
    }

    // 确认收获
    public function received(Order $order)
    {
        //权限校验
        $this->authorize('own', $order);    
        // 确认订单状态是否是 已发货
        if($order->ship_status !== \App\Models\Order::SHIP_STATUS_DELIVERED){
            throw new InvalidRequestException('发货状态不正确');
        }
        // 更新发货状态为 已收获
        $order->update(['ship_status' => \App\Models\Order::SHIP_STATUS_RECEIVED]);

        // 返回原页面
        return response()->json(['msg'=>'确认收货成功']);
    }
}
