<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
// use Illuminate\Auth\Listeners\SendEmailVerificationNotification; // 框架自带的事件
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\RegisterListener;
use App\Events\OrderPaid;
use App\Listeners\UpdateProductSoldCount;
use App\Listeners\SendOrderPaidEmail;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        Registered::class => [
            RegisterListener::class,
        ],
        
        // 
        OrderPaid::class => [
            UpdateProductSoldCount::class,  // 订单支付成功后更新商品的销量
            SendOrderPaidEmail::class     // 订单支付成功后给用户发送邮件通知
        ] 
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
