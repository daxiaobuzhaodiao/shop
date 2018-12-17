@extends('layouts.app')

@section('title', '提示')

@section('content')

    <div class="alert alert-warning text-center col-6 m-auto" role="alert"> 
        <p class="alert-heading">提示 !</p>
        <hr>
        <h5>请验证邮箱后再来~~</h5>
        <a href="/">去首页</a>
    </div>

@endsection