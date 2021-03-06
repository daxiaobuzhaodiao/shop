<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Illuminate\Support\Carbon;
use App\Events\OrderPaid;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order)
    {
        // 确认订单属于当前当前用户
        $this->authorize('own', $order);
        // 确认订单状态是否是 未支付
        if($order->paid_at || $order->closed){
            throw new InvalidRequestException('订单状态有误');
        }
        // 调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no' => $order->no, // 订单号，需要保证在商户端不重复
            'total_amount' => $order->total_amount, // 订单金额，单位元， 支持小数点后两位
            'subject' => '支付 lara-shop 的订单：'.$order->no,  // 订单标题
        ]);
    }

    // 前端回调页面
    public function alipayReturn()
    {
        // 校验提交的参数是否合法
        // $data = app('alipay')->verify();
        // dd($data);
        /*
            Collection {#390 ▼
                #items: array:12 [▼
                "charset" => "GBK"
                "out_trade_no" => "20181227131520535263"
                "method" => "alipay.trade.page.pay.return"
                "total_amount" => "157.00"
                "sign" => "WEANxPbR4kgcZS0L5bh2KXdcDbKQBVpiBodYJopYgjf8eyspeeNg075sODwcGdyqE9ua5TpPlvazoEq03Hx1/i/0HqHR20qX4BjW5FjTEuPl4PPcO6yUAQACUAaHG4CPKK0KXsgGHO7Gk6S8vUQOPQw3XbJelqXW ▶"
                "trade_no" => "2018122722001475000501684384"
                "auth_app_id" => "2016092300575102"
                "version" => "1.0"
                "app_id" => "2016092300575102"
                "sign_type" => "RSA2"
                "seller_id" => "2088102176860528"
                "timestamp" => "2018-12-27 13:15:48"
                ]
            }
        */
        
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error', ['msg' => '数据不正确']);
        }

        return view('pages.success', ['msg' => '付款成功']);
    }

    // 服务器端回调
    public function alipayNotify()
    {
        
        // 校验输入参数
        $data  = app('alipay')->verify();
        // \Log::debug('Alipay notify', $data->all());
        // 如果订单状态不是成功或者结束，则不走后续的逻辑
        // 所有校验状态：https://docs.open.alipay.com/59/103672
        if(!in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // $data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where('no', $data->out_trade_no)->first();
        // 正常来说不太可能出现支付了一笔不存在的订单，这个判断只是加强系统健壮性。
        if (!$order) {
            return 'fail';
        }
        // 如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            // 返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at'        => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no'     => $data->trade_no, // 支付宝订单号
        ]);
        
        // 调用event
        // $this->afterPaid($order);

        return app('alipay')->success();
     
    }

    // 商品销量增加事件
    public function afterPaid($order)
    {
        event(new OrderPaid($order));
    }
}
