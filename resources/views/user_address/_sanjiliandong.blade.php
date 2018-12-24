<div class="input-group mb-3 pr-5">
    <label class="my-1 col-2 text-right">省市区</label>
    <select class="custom-select mr-3" v-model="provinceId">
        <option selected>--省--</option>
        <option v-for="(name, id) in provinces" :value="id">@{{ name }}</option>
    </select>
    <select class="custom-select mr-3" v-model="cityId">
        <option selected>--市--</option>
        <option v-for="(name, id) in cities" :value="id">@{{ name }}</option>
    </select>
    <select class="custom-select mr-5" v-model="districtId">
        <option selected>--区/县--</option>
        <option v-for="(name, id) in districts" :value="id">@{{ name }}</option>
    </select>
</div>

 </select-district>

<div>
    <div class="input-group mb-3 pr-5">
        <label class="my-1 col-2 text-right">详细地址</label>
        {!! Form::text('address', null, ['class'=>'form-control mr-5']) !!}
    </div>
    <div class="input-group mb-3 pr-5">
        <label class="my-1 col-2 text-right">邮编</label>
        {!! Form::text('zip', null, ['class'=>'form-control mr-5']) !!}
    </div>
    <div class="input-group mb-3 pr-5">
        <label class="my-1 col-2 text-right">姓名</label>
        {!! Form::text('contact_name', null, ['class'=>'form-control mr-5']) !!}
    </div>
    <div class="input-group mb-3 pr-5">
        <label class="my-1 col-2 text-right">电话</label>
        {!! Form::text('contact_phone', null, ['class'=>'form-control mr-5']) !!}
    </div>
    <div class="input-group">
        {!! Form::submit(isset($address)?'修改':'添加', ['class'=>'mx-auto btn btn-info']) !!}
    </div>
</div>
<!-- 插入了 3 个隐藏的字段 -->
<!-- 通过 v-model 与 user-addresses-create-and-edit 组件里的值关联起来 -->
<!-- 当组件中的值变化时，这里的值也会跟着变 -->
<input type="hidden" name="province" v-model="province">
<input type="hidden" name="city" v-model="city">
<input type="hidden" name="district" v-model="district">