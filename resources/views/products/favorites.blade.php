@extends('layouts.app')

@section('title', '收藏列表')

@section('content')
        <h4 class="text-center">收藏列表</h4>
        @include('products._list')
        <div class="float-right">{{ $products->render() }}</div>
@endsection