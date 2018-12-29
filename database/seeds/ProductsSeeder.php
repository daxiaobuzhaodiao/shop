<?php

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductSku;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Product::truncate();
        ProductSku::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        
        $products = factory(\App\Models\Product::class, 30)->create();
        // 创建30个商品
        foreach ($products as $product) {
            // 1 创建 3 个 SKU，并且每个 SKU 的 `product_id` 字段都设为当前循环的商品 id
            $sku = factory(\App\Models\ProductSku::class, 3)->create(['product_id' => $product->id]); 
            // 2 找出价格最低的 SKU 价格，把商品价格设置为该价格
            $product->update(['price' => $sku->min('price')]);
        }
    }
}
