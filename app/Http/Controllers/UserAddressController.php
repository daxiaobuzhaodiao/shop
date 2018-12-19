<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;

class UserAddressController extends Controller
{
    // 收获地址列表
    public function index(){
        return view('user_address.index', ['addresses' => auth()->user()->address]);
    }

    // 返回添加收货地址的页面
    public function create(){
        return view('user_address.create');
    }

    // 添加收获地址
    public function store(UserAddressRequest $request)
    {
        $request->user()->address()->create($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone'
        ]));

        return redirect()->route('user_address.index');
    }

    // 返回修改收获地址的页面
    public function edit($id){
        $address = UserAddress::findOrFail($id);
        $this->authorize('own', $address);
        return view('user_address.edit', ['address'=>$address]);
    }

    // 修改收货地址
    public function update($id, UserAddressRequest $request)        // 这两个参数不分前后
    {
        $address = UserAddress::findOrFail($id);
        $this->authorize('own', $address);
        $address->update($request->only([
            'province',
            'city',
            'district',
            'address',
            'zip',
            'contact_name',
            'contact_phone'
        ]));
        return redirect()->route('user_address.index');
    }

    public function destroy($id){
        $address = UserAddress::findOrFail($id);
        $this->authorize('own', $address);
        $address->delete();
        return [];
    }
     
}
