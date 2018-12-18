@extends('layouts.app')
@section('title', '收货地址')
@section('content')
    <div class="card">
        <div class="card-header">
            <span class="pull-left">收获地址列表</span>
            <a class="float-right" href="{{ route('user_address.create') }}">新增收货地址</a>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>收货地址</th>
                        <th>邮编</th>
                        <th>联系人</th>
                        <th>联系电话</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($addresses as $address)
                        <tr>
                            <td scope="row">{{ $address->getFullAddress() }}</td>
                            <td>{{ $address->zip }}</td>
                            <td>{{ $address->contact_name }}</td>
                            <td>{{ $address->contact_phone }}</td>
                            <td>
                                <div class="form-inline">
                                {!! Form::open(['route'=>['user_address.edit', $address->id]]) !!}
                                    <button class="btn btn-sm btn-info mr-2">修改</button>
                                {!! Form::close() !!}
                                {!! Form::open(['route'=>['user_address.destroy', $address->id], 'method'=>'DELETE']) !!}
                                    {!! Form::submit('删除', ['class'=>'btn btn-sm btn-danger']) !!}
                                {!! Form::close() !!}
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> 
    </div>
@endsection