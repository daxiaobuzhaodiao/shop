<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        factory(User::class, 100)->create(); //创建100个用户
    }
}
