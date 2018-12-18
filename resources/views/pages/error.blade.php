@extends('layouts.app')

@section('title', '异常')

@section('content')
<div class="alert alert-warning text-center col-6 mx-auto" role="alert"> 
    <h5 class="alert-heading">提示 !</h5>
    <hr>
    <h5>{{ $msg }}</h5>
</div>
@endsection