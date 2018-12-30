@extends('layouts.app')
@section('title', '订单列表')

@section('content')
<div class="row">
<div class="col-lg-10 mx-auto">
<div class="card card-default">
  <div class="card-body text-center"><h5>订单列表</h5></div>
  <div class="card-body">
    <ul class="list-group">
      @foreach($orders as $order)
      <li class="list-group-item">
        <div class="card card-default">
          <div class="card-header">
            订单号：{{ $order->no }}
            <span class="pull-right">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
          </div>
          <div class="card-body">
            <table class="table">
              <thead>
                <tr>
                  <th>商品信息</th>
                  <th class="text-center">单价</th>
                  <th class="text-center">数量</th>
                  <th class="text-center">订单总价</th>
                  <th class="text-center" width="20%">状态</th>
                  <th class="text-center" width="20%">操作</th>
                </tr>
              </thead>
              @foreach($order->items as $index => $item)
              <tr>
                <td class="product-info">
                  <div class="preview">
                    <a target="_blank" href="{{ route('product.show', [$item->product_id]) }}">
                      <img src="{{ $item->product->image_url }}">
                    </a>
                  </div>
                  <div>
                    <span class="product-title">
                       <a target="_blank" href="{{ route('product.show', [$item->product_id]) }}">{{ $item->product->title }}</a>
                     </span>
                    <span class="sku-title">{{ $item->productSku->title }}</span>
                  </div>
                </td>
                <td class="sku-price text-center">￥{{ $item->price }}</td>
                <td class="sku-amount text-center">{{ $item->amount }}</td>
                @if($index === 0)
                  <td rowspan="{{ count($order->items) }}" class="text-center total-amount">￥{{ $order->total_amount }}</td>
                  <td rowspan="{{ count($order->items) }}" class="text-center">
                    @if($order->paid_at)
                      @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                        已支付
                      @else
                        {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                      @endif
                    @elseif($order->closed)
                      已关闭
                    @else
                      未支付<br>
                      请于 {{ $order->created_at->addSeconds(config('app.order_tt'))->format('H:i') }} 前完成支付<br>
                      否则订单将自动关闭
                      <!-- 只有未支付的订单才显示付款按钮 -->
                      <a class="btn btn-sm btn-primary" href="{{ route('payment.alipay', $order->id) }}">支付宝付款</a>
                    @endif
                    <!-- 未付款 并且 没有被关闭的订单  才显示支付按钮 -->
                  </td>
                  <td rowspan="{{ count($order->items) }}" class="text-center">
                    <a class="btn btn-primary btn-sm" href="{{ route('order.show', $order->id) }}">查看订单</a>
                    @if($order->paid_at)
                      <a class="btn btn-success btn-sm" href="{{ route('order.review.show', $order->id) }}">{{ $order->reviewed ? '查看评价' : '评价' }}</a>
                    @endif
                  </td>
                  
                @endif
              </tr>
              @endforeach
            </table>
          </div>
        </div>
      </li>
      @endforeach
    </ul>
    <div class="pull-right">{{ $orders->render() }}</div>
  </div>
</div>
</div>
</div>
@endsection