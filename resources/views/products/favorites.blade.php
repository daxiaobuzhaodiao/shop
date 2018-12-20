@extends('layouts.app')

@section('title', '收藏列表')

@section('content')
    <h3>我的收藏</h3>
    @include('products._list')
    <div class="float-right">{{ $products->render() }}</div>
@endsection