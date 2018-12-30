<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CouponCode;
use Illuminate\Support\Carbon;
use App\Exceptions\CouponCodeUnavailableException;

class CouponController extends Controller
{
    public function check($code)
    {
        // 判断优惠券是否存在
        if (!$record = CouponCode::where('code', $code)->first()){
            // abort(404); //abort() 方法可以直接中断我们程序的运行，接受的参数会变成 Http 状态码返回 (不用他)
            throw new CouponCodeUnavailableException('优惠券不存在');
        }
        $record->checkAvailable(auth()->user());
        return $record;
    }
}
