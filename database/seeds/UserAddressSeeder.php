<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAddress;

class UserAddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        UserAddress::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        // 地址必须要关联到用户
        User::all()->each(function (User $user){
            factory(UserAddress::class, random_int(1, 3))->create(['user_id' => $user->id]);  // create() 参数是个数组否则报错
        });
        /*
        1 User::all() 从数据获取所有的用户（我们之前通过 UsersSeeder 生成了 100 条），并返回一个集合 Collection。
        2 ->each() 是 Collection 的一个方法，与 foreach 类似，循环集合中的每一个元素，将其作为参数传递给匿名函数，在这里集合里的元素都是 User 类型。
        3 factory(UserAddress::class, random_int(1, 3)) 对每一个用户，产生一个 1 - 3 的随机数作为我们要为个用户生成地址的个数。
        4 create(['user_id' => $user->id]) 将随机生成的数据写入数据库，同时指定这批数据的 user_id 字段统一为当前循环的用户 ID。
        */
    }
}
