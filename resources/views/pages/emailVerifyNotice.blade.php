@extends('layouts.app')

@section('title', '提示')

@section('content')

    <div class="alert alert-warning text-center col-6 mx-auto" role="alert"> 
        <h5 class="alert-heading">提示 !</h5>
        <hr>
        <h5>请验证邮箱后再来~~</h5>
        <hr>
        <a class="btn btn-primary" href="{{ route('email_verification.send') }}">点击发送验证邮件！</a>
    </div>

@endsection