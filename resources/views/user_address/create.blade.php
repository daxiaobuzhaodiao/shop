@extends('layouts.app')
@section('title', '添加收货地址')

@section('content')
    <div class="col-10 card mx-auto">
        <div class="card-body text-center"><h5>添加收货地址</h5></div>
        <div class="card-body">
            <!-- 输出后端报错开始 -->
            @include('user_address._error')
            <!-- 输出后端报错结束 -->
            <!-- inline-template 代表通过内联方式引入组件 -->
            <user-addresses-create-and-edit inline-template>
                {!! Form::open(['route'=>'user_address.store', 'method'=>'POST']) !!}
                    <select-district @change="onDistrictChanged" inline-template>
                    @include('user_address._sanjiliandong')
                {!! Form::close() !!}
            </user-addresses-create-and-edit>
        </div>
    </div>
@endsection