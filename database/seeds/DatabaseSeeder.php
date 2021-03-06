<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(UserAddressSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(CouponCodeSeeder::class);
        $this->call(OrderSeeder::class);
    }
}
