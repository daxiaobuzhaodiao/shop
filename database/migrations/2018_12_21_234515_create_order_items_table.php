<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id');    // 所属订单id 外键
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('product_id');  // 对应商品id 外键
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('product_sku_id');  // 对应商品sku的id
            $table->foreign('product_sku_id')->references('id')->on('product_skus')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('amount');  // 数量
            $table->decimal('price', 10, 2);    // 单价
            $table->unsignedInteger('rating')->nullable();  // 用户打分
            $table->text('review')->nullable(); //用户评价内容
            $table->timestamp('reviewed_at')->nullable();   // 评价时间
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
