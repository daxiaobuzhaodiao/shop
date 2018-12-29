<?php

use Illuminate\Database\Seeder;
use App\Models\CouponCode;

class CouponCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        CouponCode::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        factory(App\Models\CouponCode::class, 20)->create();
    }
}
