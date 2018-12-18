@extends('layouts.app')
@section('title', '新增收货地址')

@section('content')
    <div class="card mb-3" style="width:50rem;margin:0 auto">
        <div class="card-header text-center"><h3>新增收货地址</h3></div>
        <div class="card-body">
            <!-- 输出后端报错开始 -->
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <h4>有错误发生：</h4>
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li><i class="glyphicon glyphicon-remove"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <!-- 输出后端报错结束 -->
            <!-- inline-template 代表通过内联方式引入组件 -->
            <user-addresses-create-and-edit inline-template>
            <form class="mx-auto" action="{{ route('user_address.store') }}" method="POST">
                @csrf
                <!-- inline-template 代表通过内联方式引入组件 -->
                <select-district @change="onDistrictChanged" inline-template>
                    <div class="input-group mb-3 mr-3">
                        <label class="my-1 col-2 text-right">省市区</label>
                        <select class="custom-select mr-3" v-model="provinceId">
                            <option selected>--选择省--</option>
                            <option v-for="(name, id) in provinces":value="id">@{{ name }}</option>
                        </select>
                        <select class="custom-select mr-3" v-model="cityId">
                            <option selected>--选择市--</option>
                            <option v-for="(name, id) in cities":value="id">@{{ name }}</option>
                        
                        </select>
                        <select class="custom-select" v-model="districtId">
                            <option selected>--选择区--</option>
                            <option v-for="(name, id) in districts" :value="id">@{{ name }}</option>
                        </select>
                    </div>
                </select-district>
                <!-- 插入了 3 个隐藏的字段 -->
                <!-- 通过 v-model 与 user-addresses-create-and-edit 组件里的值关联起来 -->
                <!-- 当组件中的值变化时，这里的值也会跟着变 -->
                <input type="hidden" name="province" v-model="province">
                <input type="hidden" name="city" v-model="city">
                <input type="hidden" name="district" v-model="district">
                <div>
                    <div class="input-group mb-3">
                        <label class="my-1 col-sm-2 text-right">详细地址</label>
                        <input type="text" class="form-control" name="address" value="">
                    </div>
                    <div class="input-group mb-3">
                        <label class="my-1 col-2 text-right">邮编</label>
                        <input type="text" class="form-control" name="zip" value="">
                    </div>
                    <div class="input-group mb-3">
                        <label class="my-1 col-2 text-right">姓名</label>
                        <input type="text" class="form-control" name="contact_name" value="">
                    </div>
                    <div class="input-group mb-3">
                        <label class="my-1 col-2 text-right">电话</label>
                        <input type="text" class="form-control" name="contact_phone" value="">
                    </div>
                    <div class="input-group">
                        <button type="submit" class="mx-auto btn btn-primary">添加</button>
                    </div>
                </div>
            </form>
            </user-addresses-create-and-edit>
        </div>
    </div>
@endsection