<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->increments('id');   // 主键
            $table->unsignedInteger('user_id'); // 外键
            $table->string('province'); //省
            $table->string('city'); // 市
            $table->string('district'); // 区
            $table->string('address');  // 详细地址
            $table->unsignedInteger('zip')->nullable(); // 邮编
            $table->string('contact_name'); // 联系人姓名
            $table->string('contact_phone');    // 联系电话
            $table->dateTime('last_used_at')->nullable(); // 最后一次使用时间
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_addresses');
    }
}
