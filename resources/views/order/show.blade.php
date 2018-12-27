@extends('layouts.app')

@section('title', '订单详情')

@section('content')
<div class="row">
    <div class="col-lg-10 mx-auto">
    <div class="card card-default">
      <div class="card-header text-center">
        <h4>订单详情</h4>
      </div>
      <div class="card-body">
        <table class="table">
          <thead>
            <tr>
              <th>商品信息</th>
              <th class="text-center">单价</th>
              <th class="text-center">数量</th>
              <th class="text-right item-amount">小计</th>
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
            <td class="sku-price text-center vertical-middle">￥{{ $item->price }}</td>
            <td class="sku-amount text-center vertical-middle">{{ $item->amount }}</td>
            <td class="item-amount text-right vertical-middle">￥{{ number_format($item->price * $item->amount, 2, '.', '') }}</td>
          </tr>
          @endforeach
          <tr><td colspan="4"></td></tr>
        </table>
        <div class="order-bottom">
          <div class="order-info">
            <div class="line"><div class="line-label">收货地址：</div><div class="line-value">{{ join(' ', $order->address) }}</div></div>
            <div class="line"><div class="line-label">订单备注：</div><div class="line-value">{{ $order->remark ?: '-' }}</div></div>
            <div class="line"><div class="line-label">订单编号：</div><div class="line-value">{{ $order->no }}</div></div>
            <!-- 输出物流状态 -->
            <div class="line">
              <div class="line-label">物流状态：</div>
              <div class="line-value">{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</div>
            </div>
            <!-- 如果有物流信息则展示 -->
            @if($order->ship_data)
            <div class="line">
              <div class="line-label">物流信息：</div>
              <div class="line-value">{{ $order->ship_data['express_company'] }} {{ $order->ship_data['express_no'] }}</div>
            </div>
            @endif

          </div>
          <div class="order-summary text-right">
            <div class="total-amount">
              <span>订单总价：</span>
              <div class="value">￥{{ $order->total_amount }}</div>
            </div>
            <div>
              <span>订单状态：</span>
              <div class="value">
                @if($order->paid_at)
                  @if($order->refund_status === \App\Models\Order::REFUND_STATUS_PENDING)
                    已支付
                  @else
                    {{ \App\Models\Order::$refundStatusMap[$order->refund_status] }}
                  @endif
                @elseif($order->closed)
                  已关闭
                @else
                  未支付
                @endif
              </div>
            </div>
            @if(!$order->paid_at && !$order->closed)
            <a class="btn btn-sm btn-primary mt-2" href="{{ route('payment.alipay', $order->id) }}">支付宝支付</a>
            @endif
            @if($order->ship_status === \App\Models\Order::SHIP_STATUS_DELIVERED)
              <button class="btn btn-primary btn-sm btn-receive mt-2">确认收获</button>
            @endif
          </div>
        </div>
      </div>
    </div>
    </div>
    </div>
@endsection

@section('customJS')
    <script>
      $(document).ready(function() {
        $('.btn-receive').click(function () {
          Swal({
            title: '',
            text: "您确定么?",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '确认',
            cancelButtonText: '取消'
          }).then((result) => {
            // 如果确定则 发请求
            if (result.value) {
              axios.post('{{ route('order.received', $order->id) }}').then((res)=>{
                // 请求成功 弹窗
                if(res.status === 200){
                  Swal(
                    '',
                    res.data.msg,
                    'success'
                  ).then(()=>{
                    location.reload();
                  })
                }
              })
            }
          })
        })
      })
    </script>
@endsection