@extends('layouts.app')

@section('title', '收藏列表')

@section('content')
<div class="row">
  <div class="col-lg-10 mx-auto">
    <div class="card card-default">
      <div class="card-body text-center"><h5>我的购物车</h5></div>
      <div class="card-body">
        <table class="table table-hover">
          <thead>
          <tr>
            <th><input type="checkbox" id="select-all"></th>
            <th>商品信息</th>
            <th>单价</th>
            <th>数量</th>
            <th>操作</th>
          </tr>
          </thead>
          <tbody class="product_list">
          @foreach($cartItems as $item)
            <tr data-id="{{ $item->productSku->id }}">
              <td>
                <!-- 默认选中，调用关系确定当前的sku此时此刻是否正在售卖（加入购物车之后会有被下架的可能，所以要进行判断） -->
                <input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
              </td>

              <td class="product_info">
                <div class="preview">
                  <!-- 点击跳转到当前sku对应的商品的详情页 -->
                  <a target="_blank" href="{{ route('product.show', $item->productSku->product_id) }}">
                    <img src="{{ $item->productSku->product->image_url }}">
                  </a>
                </div>
                <!-- 同样的判断sku对应的商品此时此刻是否正在收买，如果没有正在售卖则 添加 class="not_on_sale" 改变样式（删除线） -->
                <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
                  <span class="product_title">
                    <a target="_blank" href="{{ route('product.show', [$item->productSku->product_id]) }}"><strong>{{ $item->productSku->product->title }}</strong></a>
                  </span>
                  <span class="sku_title">规格：{{ $item->productSku->title }}</span>
                  @if(!$item->productSku->product->on_sale)
                    <span class="warning">该商品已下架</span>
                  @endif
                </div>
              </td>

              <td><span class="price">￥{{ $item->productSku->price }}</span></td>

              <td>
                <input type="text" class="form-control input-sm amount" @if(!$item->productSku->product->on_sale) disabled @endif name="amount" value="{{ $item->amount }}">
              </td>

              <td>
                <button class="btn btn-xs btn-danger btn-remove">移除</button>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
        <!-- 分页 -->
        {{-- <div>{{ $cartItems->render() }}</div> --}}
        <hr>
        <!-- 选择收获地址 -->
        <form id="order-form" class="col-10 mx-auto px-0">
          <div class="form-group">
            <label class="font-weight-bold">收获地址</label>
            <select class="custom-select" name="address">
              @foreach ($addresses as $address)
                <option value="{{ $address->id }}">{{ $address->full_address }} {{ $address->zip }} {{ $address->contact_name }} {{ $address->contact_phone }}</option>
              @endforeach
            </select>
          </div>
          <!-- 备注 -->
          <div class="form-group">
            <label class="font-weight-bold">备注</label>
            <textarea class="form-control" name="remark" rows="3"></textarea>
          </div>
          <!-- 优惠码开始 -->
          <label class="font-weight-bold">优惠码</label>
          <div class="row form-group">
            <div class="col-5">
              <input type="text" class="form-control" name="coupon_code" placeholder="如果没有可以不填~~">
              <span class="form-text text-muted" id="coupon_desc"></span>
            </div>
            <div class="col-3">
              <button type="button" class="btn btn-success" id="btn-check-coupon">检查</button>
              <button type="button" class="btn btn-danger" id="btn-cancel-coupon" style="display:none">取消</button>
            </div>
          </div>
          <!-- 优惠码结束 -->
          <div class="form-group">
            <button class="btn btn-primary btn-create-order pull-right">提交订单</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('customJS')
    <script>
        $(document).ready(function () {
            // 删除购车车
            $('.btn-remove').click(function () {
              // closest() 方法可以获取到匹配选择器的第一个祖先元素 从当前元素开始的
              // parents() 方法类似 从父元素开始查找的
              var id = $(this).closest('tr').data('id');
              Swal({
                  title: '确定要删除么?',
                  type: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  cancelButtonText: '取消',
                  confirmButtonText: '删除'
              }).then((result) => {
                  if (result.value) {
                      axios.delete('/cart/'+id).then(function () {
                          window.location.reload();
                      })
                  }
              })
            });
            
            // 购物车全选
            $('#select-all').change(function() {
              // 当获得的这个属性的值是true和false 的时候使用prop  其他时候就使用attr
              var checked = $(this).prop('checked');
              $('input[name=select][type=checkbox]:not([disabled])').each(function() {
                $(this).prop('checked', checked); 
              });
            });

            // 提交订单
            $('.btn-create-order').click(function () {
              // 构建请求参数
              var req = {
                address_id: $('#order-form').find('select[name=address]').val(),  // 收货地址
                remark: $('#order-form').find('textarea[name=remark]').val(), // 备注
                items: [],      // sku_id 和 amount
              };

              // 获得参数中的 items[] 这个数组的数据
              $('table tr[data-id]').each(function () {
                // 1 忽略掉被禁用的和没有被选中的sku单品
                var $checkbox = $(this).find('input[name=select][type=checkbox]');
                if ($checkbox.prop('disabled') || !$checkbox.prop('checked')) {
                  return;
                }
                // 2 判断是否填写了购买数量
                var $input = $(this).find('input[name=amount]');
                if ($input.val() == 0 || isNaN($input.val())) {
                  return;
                }
                // 3 将当前sku_id和数量 push给items[]
                req.items.push({
                  sku_id: $(this).data('id'),
                  amount: $input.val(),
                })
              });
            
              // 发送请求
              axios.post('{{ route('order.store') }}', req)
                .then(function (res){
                      // console.log(res.data);
                      Swal('', '成功', 'success')
                      .then(function () {
                        location.href = '/order/' + res.data.id
                      })
                }).catch(function (err){
                  if(err.response.status == 422){
                    let html = '<div>';
                    $.each(err.response.data.errors, function(index, value){
                      html += value[0] + '<br/>';
                    })
                    html+='</div>';
                    Swal({
                      type: 'error',
                      title: html,
                    })
                  }else{
                    Swal('', '系统错误，请联系客服', 'warning')
                  }
                })
                // 阻止表单提交的行为
                return false;
            });


            // 优惠码
            $('#btn-check-coupon').click(function () {
              var code = $('input[name=coupon_code]').val();
              if(!code){
                Swal('', '请输入优惠码', 'warning');
                return
              }
              axios.get('coupon/' + code).then((res)=>{
                $('#coupon_desc').text(res.data.description)
                $('input[name=coupon_code]').prop('readonly', true)
                $('#btn-check-coupon').hide()
                $('#btn-cancel-coupon').show()
              }).catch((err)=>{
                if(err.response.status == 404){
                  Swal('', '优惠码不存在', 'error')
                }else if(err.response.status === 401){
                  Swal('', err.response.data.msg, 'error')
                }else{
                  Swal('', '系统错误，请联系客服', 'error')
                }
              })
            }); 

            // 优惠券的取消按键单击事件
            $('#btn-cancel-coupon').click(function() {
              $('#btn-check-coupon').show()
              $('#btn-cancel-coupon').hide()
              $('#coupon_desc').text('')
              $('input[name=coupon_code]').val('')
              $('input[name=coupon_code]').prop('readonly', false)
            })
        });
    </script>
@endsection