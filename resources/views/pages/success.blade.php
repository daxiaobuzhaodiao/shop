@extends('layouts.app')
@section('title', '操作成功')
@section('content')
    <div class="alert alert-success text-center col-6 m-auto" role="alert"> 
        <p class="alert-heading">操作成功 !</p>
        <hr>
        <h5>{{ $msg }}</h5>
        <a href="/">去首页</a>
    </div>
@endsection