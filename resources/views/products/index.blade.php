@extends('layouts.app')

@section('title', '商品列表')

@section('content')
    <div class="row p-0">
        @foreach($products as $product)
        <div class="card-deck col-3 mb-3">
                <div class="card">
                    <img class="card-img-top border" src="{{ $product->image_url }}" alt="Card image cap">
                    <div class="card-body text-center p-1">
                        <div>
                            <div class="card-title"><b>$</b>{{ $product->price }}</div>
                            <div>{{ $product->title }}</div>
                        </div>
                        <div class="px-3 mx-auto">
                            <span class="float-left">销量:{{ $product->sold_count }}</span>
                            <span class="float-right">评分:{{ $product->review_count }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="pull-right">{{ $products->render() }}</div>
    </div>
@endsection