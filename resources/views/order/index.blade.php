@extends('layouts.app')
@section('title', '订单列表')

@section('content')
  
  
  <div class="card text-center">
    <div class="card-header text-center">
      <h4>订单列表</h4>
    </div>
    <div class="card-body">
      @foreach($orders as $order)
      <div class="card mb-3">
        <div class="card-header">
          <div class="float-left">订单号：{{ $order->no }}</div>
          <div class="float-right">创建时间：{{ $order->created_at }}</div>
        </div>
        <div class="card-body">
          <table class="table table-bordered">
            <thead class="thead">
              <tr>
                <th>商品信息</th>
                <th>单价</th>
                <th>数量</th>
                <th>订单总价</th>
                <th>数量</th>
                <th>操作</th>
              </tr>
              </thead>
              <tbody>
                @foreach ($order->items as $index => $item)
                  <tr>
                        <td class="product-info">
                          <div class="preview float-left mr-2">
                            <img src="{{ $item->product->image_url }}" alt="" width="50px" height="50px">
                          </div>
                          <div class="float-left">
                            <div class="product-title">
                              {{ $item->product->title }}
                            </div>
                            <div class="sku-title">
                              {{ $item->productSku->title }}
                            </div>
                          </div>
                        </td>
                        <td>{{ $item->price }}</td>
                        <td>{{ $item->amount }}</td>
                        @if($index === 0)
                        <td rowspan="{{ count($order->items) }}">{{ $order->total_amount }}</td>
                        <td rowspan="{{ count($order->items) }}">
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
                              请于 {{ $order->created_at->addSeconds(config('app.order_ttl'))->format('H:i') }} 前完成支付<br>
                              否则订单将自动关闭
                            @endif
                        </td>
                        <td rowspan="{{ count($order->items) }}"><a href="" class="btn btn-primary btn-sm">查看订单</a></td>
                        @endif
                  </tr>
                @endforeach
              </tbody>
          </table>
        </div>
      </div>
      @endforeach
      <div class="float-right">{{ $orders->render() }}</div>
    </div>
  </div>
@endsection