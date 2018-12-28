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
                            <td scope="row">{{ $address->full_address }}</td>
                            <td>{{ $address->zip }}</td>
                            <td>{{ $address->contact_name }}</td>
                            <td>{{ $address->contact_phone }}</td>
                            <td>
                                <div class="form-inline">
                                {!! Form::open(['route'=>['user_address.edit', $address->id], 'method'=>'get']) !!}
                                    {!! Form::submit('修改', ['class'=>'btn btn-sm btn-info mr-2']) !!}
                                {!! Form::close() !!}

                                <button class="btn btn-sm btn-danger del-address" data-id="{{ $address->id }}">删除</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> 
    </div>
@endsection

@section('customJS')
    <script>
        $(document).ready(function (){
            $('.del-address').click(function (){
                let id = $(this).data('id');
                // 下方是sweetalert2 代码
                const swalWithBootstrapButtons = Swal.mixin({
                    confirmButtonClass: 'btn btn-success',
                    cancelButtonClass: 'btn btn-danger mr-5',
                    buttonsStyling: false,
                })

                swalWithBootstrapButtons({
                    title: '您确定么?',
                    text: "一旦删除将不会被恢复!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '是，确定!',
                    cancelButtonText: '不，取消!',
                    reverseButtons: true
                }).then((result) => {
                // console.log(result.value);  // 确定返回true  取消无返回
                    if (result.value) {
                        axios.delete( '/user_address/' + id ).then((res)=>{
                            swalWithBootstrapButtons(
                                '已删除',
                                '您的收获地址已删除',
                                'success'
                            ).then(()=>{
                                // 确定后就 刷新页面
                                window.location.reload();
                            })
                        })
                      
                    } else {
                        swalWithBootstrapButtons(
                            '已取消',
                            '您的收获地址很安全 :)',
                            'error'
                        )
                    }
                })
            })
        })
    </script>
@endsection