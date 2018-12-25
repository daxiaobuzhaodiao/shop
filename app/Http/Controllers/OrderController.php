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
use App\Services\OrderService;

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
        
    // 增 （注：这里也执行了删除购物车的操作所以调用CartService的删除方法删除购物车）
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
}
