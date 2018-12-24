@extends('layouts.app')
@section('title', '修改收货地址')

@section('content')
    <div class="card mb-3 mx-auto" style="width:50rem">
        <div class="card-header text-center"><h3>修改收货地址</h3></div>
        <div class="card-body">
            <!-- 输出后端报错开始 -->
            @include('user_address._error')
            <!-- 输出后端报错结束 -->
            <!-- inline-template 代表通过内联方式引入组件 -->
            <user-addresses-create-and-edit inline-template>
                {!! Form::model($address,['route'=>['user_address.update', $address->id], 'method'=>'PUT']) !!}
                    <select-district :init-value="{{ json_encode([$address->province, $address->city, $address->district]) }}" @change="onDistrictChanged" inline-template>
                    @include('user_address._sanjiliandong')
                  
                {!! Form::close() !!}
            </user-addresses-create-and-edit>
        </div>
    </div>
@endsection