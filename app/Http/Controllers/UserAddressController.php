<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserAddressRequest;
use App\Models\UserAddress;

class UserAddressController extends Controller
{
    // 地址列表
    public function index(){
        return view('user_address.index', ['addresses' => auth()->user()->address]);
    }

    // 返回添加页面
    public function create(){
        return view('user_address.create');
    }

    // 添加
    public function store(UserAddressRequest $request)
    {
        //通过白名单的方式从用户提交的数据里获取我们所需要的数据，这种方式存储不需要再模型中定义 $fillable 数组
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

    // 返回修改页面
    public function edit($id){
        $address = UserAddress::findOrFail($id);
        // 校验当前的用户是否具有修改此地址的权限
        
        //authorize('own', $address) 方法会获取第二个参数 $address 的类名: App\Models\UserAddress
        //然后在 AuthServiceProvider 类的 $policies 属性中寻找对应的策略，在这里就是 App\Policies\UserAddressPolicy
        //找到之后会实例化这个策略类，再调用名为 own() 方法，如果 own() 方法返回 false 则会抛出一个未授权的403异常。
        $this->authorize('own', $address);
        return view('user_address.edit', ['address'=>$address]);
    }

    // 修改
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

    // 删除
    public function destroy($id){
        $address = UserAddress::findOrFail($id);
        $this->authorize('own', $address);
        $address->delete();
        return [];
    }
     
}
