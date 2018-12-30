<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CouponCode;
use Illuminate\Support\Carbon;

class CouponController extends Controller
{
    public function check($code)
    {
        // 判断优惠券是否存在
        if (!$record = CouponCode::where('code', $code)->first()){
            abort(404); //abort() 方法可以直接中断我们程序的运行，接受的参数会变成 Http 状态码返回
        }
        // 判断优惠券是否已启用 如果没有启用则视为不存在
        if(!$record->enabled){
            abort(404);
        }
        // 判断优惠券是否已经用尽
        if($record->total - $record->used <= 0){
            return response()->json(['msg' => '该优惠券已经已被兑换完', 401]);
        }
        // 判断优惠券的使用时间
        if($record->not_before && $record->not_before->gt(Carbon::now())){
            return response()->json(['msg'=>'该优惠券现在还不能使用'], 401);
        }
        if($record->not_after && $record->not_after->lt(Carbon::now())){
            return response()->json(['msg'=>'该优惠券已过期'], 401);
        }
        return $record;
    }
}
