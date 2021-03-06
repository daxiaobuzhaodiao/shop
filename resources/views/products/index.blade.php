@extends('layouts.app')

@section('title', '商品列表')

@section('content')
    @if(session('message') && session('user'))
        <div class="alert alert-warning alert-dismissible fade show email-verify-alert" role="alert">
            <strong>{{ session('user')->name }}</strong> {{ session('message') }} 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="row mb-2">
        <form class="form-inline my-2 my-lg-0 w-100 search-form" action="{{ route('product.index') }}" method="get">
            <div class="col-6 text-left">
                <input class="form-control mr-sm-2" name="search" type="text" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
            </div>
            <div class="col-6 text-right">
                <select class="custom-select w-50" name="order">
                    <option value="">排序</option>
                    <option value="price_asc">价格从低到高</option>
                    <option value="price_desc">价格从高到低</option>
                    <option value="sold_count_desc">销量从高到低</option>
                    <option value="sold_count_asc">销量从低到高</option>
                    <option value="rating_desc">评价从高到低</option>
                    <option value="rating_asc">评价从低到高</option>
                </select>
            </div>
        </form>
    </div>
    @include('products._list')
    <div class="float-right">
        {{ $products->render() }}
    </div>
@endsection

@section('customJS')
    <script>

        $(document).ready(function (){
            setTimeout(() => {
                $(".email-verify-alert").slideToggle("slow");
            }, 5000);


            var filters =  {!! json_encode($filters) !!}// 注意 Unexpected token & 报错 不能使用{{ json_encode($filters) }}
            $('.search-form input[name=search]').val(filters.search);
            $('.search-form select[name=order]').val(filters.order);

            $('.search-form select[name=order]').change(function (){
                $('.search-form').submit();
            })
        })
        
    </script>
@endsection