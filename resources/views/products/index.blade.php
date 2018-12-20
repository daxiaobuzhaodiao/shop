@extends('layouts.app')

@section('title', '商品列表')

@section('content')
    <div class="row mb-2">
        <form class="form-inline my-2 my-lg-0 w-100 search-form" action="{{ route('products.index') }}" method="get">
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


    <div class="row">
        @foreach($products as $product)
            <div class="card-deck col-md-3 col-sm-4 mb-3 p-0 m-0">
                <div class="card p-1">
                    <a href="{{ route('products.show', $product->id) }}"><img class="card-img-top border" src="{{ $product->image_url }}" alt="Card image cap"></a>
                    <div class="col card-body px-3 py-0">
                        <div class="col">
                            <div><b>$</b>{{ $product->price }}</div>
                            <div>{{ $product->title }}</div>
                        </div>
                        <div class="col mx-auto">
                            <span class="float-left">销量:{{ $product->sold_count }}</span>
                            <span class="float-right">评分:{{ $product->rating }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="float-right">
        {{ $products->render() }}
    </div>
@endsection

@section('customJS')
    <script>
        // Unexpected token & 报错  需要使用下面的方式哦~~~
        var filters =  {!! json_encode($filters) !!}
        $(document).ready(function (){
            $('.search-form input[name=search]').val(filters.search);
            $('.search-form select[name=order]').val(filters.order);

            $('.search-form select[name=order]').change(function (){
                $('.search-form').submit();
            })
        })
    </script>
@endsection