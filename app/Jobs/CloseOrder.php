<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        $this->delay($delay);   // delay这个方法（）是哪儿来的
    }

    public function handle()
    {
        // 如果已经支付则不关闭订单  直接退出
        if($this->order->paid_at) {
            return;
        }

        // 通过事务执行sql
        \DB::transaction(function () {
            // 将订单的closed字段设置为true
            $this->order->update(['closed'=>true]);
            // 循环遍历订单的sku 将订单的中的数量加回到sku的库存中
            foreach($this->order->items as $item){
                $item->productSku->addStock($item->amount);
            }
        });
    }
}
