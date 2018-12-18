@extends('layouts.app')
@section('title', '收货地址')
@section('content')
@dd($address)
<div class="container">
    <h2>卡片头部和底部</h2>
    <div class="card">
      <div class="card-header">头部</div>
      <div class="card-body">
        <table class="table table-borderd table-hover">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td scope="row"></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td scope="row"></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div> 
      <div class="card-footer">底部</div>
    </div>
  </div>
@endsection