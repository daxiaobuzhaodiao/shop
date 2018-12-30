<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use App\Services\OrderService;
use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Carbon;
use App\Http\Requests\SendReviewRequest;
use App\Events\OrderReviewed;
use App\Models\CouponCode;
use App\Exceptions\CouponCodeUnavailableException;

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
        $coupon = null;
        $address = UserAddress::findOrFail($request->address_id);
        // 如果用户提交了优惠券
        if($code = $request->code){
            $coupon = CouponCode::where('code', $code)->first();
            if(!$coupon){
                throw new CouponCodeUnavailableException('该优惠券不存在');
            }
        }
        // dd($coupon);
        $order = $orderService->store($request->user(), $address, $request->remark, $request->items, $coupon);
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

    // 返回评价页面
    public function review(Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断是否已支付
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 使用load预加载关系  避免n+1的性能问题
        return view('order.review')->with(['order'=>$order->load('items.productSku', 'items.product')]);
    }

    // 添加评价内容
    public function sendReview(SendReviewRequest $request, Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 判断是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        $reviews = $request->input('reviews');
        // 开启事务
        \DB::transaction(function () use ($reviews, $order) {
            // 遍历用户提交的数据
            foreach ($reviews as $review) {
                $orderItem = $order->items()->find($review['id']);
                // 保存评分和评价
                $orderItem->update([
                    'rating'      => $review['rating'],
                    'review'      => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
        });    
        
        event(new OrderReviewed($order));

        return redirect()->back();
    }

    // 退款
    public function applyRefund(Order $order, Request $request)
    {
        // 1 校验订单是否属于当前用户
        $this->authorize('own', $order);
        // 2 判断订单是否已付款
        if(!$order->paid_at){
            throw new InvalidRequestException('该订单未付款，不可退款');
        }
        // 3 判断订单退款状态是否正确  退款状态一共是5种  1 未退款 2 已申请退款 3 退款中 4 退款成功 5 退款失败
        if($order->refund_status !== Order::REFUND_STATUS_PENDING){
            throw new InvalidRequestException('该订单已经申请过退款，请勿重新申请');
        }
        // 4 将退款理由存进  extra 字段中
        $extra = $order->extra ? : [];
        $extra['refund_reason'] = $request->reason;
        // 5 修改订单的退款状态和备注
        $order->update([
            'refund_status' =>  Order::REFUND_STATUS_APPLIED,
            'extra' => $extra
        ]);

        return $order;
    }
}
