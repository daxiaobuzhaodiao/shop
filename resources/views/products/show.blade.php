@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
<div class="col-lg-10 m-auto">
<div class="panel panel-default">
  <div class="panel-body product-info">
    <div class="row">
      <div class="col-sm-5">
        <img class="cover" src="{{ $product->image_url }}" alt="">
      </div>
      <div class="col-sm-7">
        <div class="title">{{ $product->title }}</div>
        <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
        <div class="sales_and_reviews">
          <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
          <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span></div>
          <div class="rating" title="评分 {{ $product->rating }}">评分 <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
        </div>
        <div class="skus">
          <label>选择</label>
          <div class="btn-group" data-toggle="buttons">
            @foreach($product->sku as $sku)
            <label
                class="btn btn-default sku-btn mr-1 border"
                data-price="{{ $sku->price }}"
                data-stock="{{ $sku->stock }}"
                {{-- data-toggle="tooltip" --}}
                title="{{ $sku->description }}"
                data-placement="bottom">
                <input style="display:none" type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
            </label>
            @endforeach
          </div>
        </div>
        <div class="cart_amount"><label>数量</label><input type="text" class="form-control input-sm" value="1"><span>件</span><span class="stock"></span></div>
        <div class="buttons">
          @if($favored)
          <button class="btn btn-success btn-disfavor">取消收藏</button>
          @else
          <button class="btn btn-success btn-favor">❤ 收藏</button>
          @endif
          <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
        </div>
      </div>
    </div>
    <div class="product-detail mt-3">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="description-tab" data-toggle="tab" href="#description" role="tab" aria-controls="description" aria-selected="true">description</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Profile</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabContent">
            <!-- 因为我们后台编辑商品详情用的是富文本编辑器，提交的内容是 Html 代码，此处需要原样输出而不需要进行 Html 转义 -->
            <div class="tab-pane fade show active p-3" id="description" role="tabpanel" aria-labelledby="description-tab">{!! $product->description !!}</div>
            <div class="tab-pane fade p-3" id="profile" role="tabpanel" aria-labelledby="profile-tab">...</div>
        </div>
    </div>
  </div>
</div>
</div>
</div>
@endsection

@section('customJS')
    <script>
        $(document).ready(function () {
            // $('[data-toggle="tooltip"]').tooltip({trigger: 'hover'});
            $('.sku-btn').click(function () {
              $('.product-info .price span').text($(this).data('price'));
              $('.product-info .stock').text('库存：' + $(this).data('stock') + '件');
            });

            // 收藏商品
            $('.btn-favor').click(function (){
              axios.post('{{ route('product.favorite', $product->id) }}')
                .then(function (res){
                  // 代码进到这里表示请求成功
                  Swal('', '收藏成功', 'success')
                    .then(function (){
                      document.location.reload();
                    })
                }).catch(function (err){
                  console.log(err.response.data);
                  if(err.response.status == 401){
                    Swal('', '请先登录', 'warning');
                  }else if(err.response.data.msg){
                    Swal('', err.response.data.msg, 'warning')
                  }else{
                    Swal('','未知错误，请稍后再试', 'warning')
                  }
                })
            })

            // 取消收藏
            $('.btn-disfavor').click(function (){
              axios.delete('{{ route('product.disfavorite', $product->id) }}')
                .then(function (res){
                  Swal('','取消成功', 'success')
                    .then(function () {
                      document.location.reload();
                    })
                }).catch(function (err){
                  console.log(err.response);
                  
                })
            })
        });
    </script>
@endsection