<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductVariant;
use App\Models\Product;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        // iPhone 15 Pro variants
        $iphone = Product::where('name', 'iPhone 15 Pro')->first();
        if ($iphone) {
            ProductVariant::create([
                'product_id' => $iphone->product_id,
                'variant_name' => '128GB - Natural Titanium',
                'additional_price' => 0.00,
                'stock_quantity' => 15,
            ]);
            ProductVariant::create([
                'product_id' => $iphone->product_id,
                'variant_name' => '256GB - Natural Titanium',
                'additional_price' => 100.00,
                'stock_quantity' => 20,
            ]);
            ProductVariant::create([
                'product_id' => $iphone->product_id,
                'variant_name' => '512GB - Blue Titanium',
                'additional_price' => 300.00,
                'stock_quantity' => 10,
            ]);
        }

        // Samsung Galaxy S24 Ultra variants
        $samsung = Product::where('name', 'Samsung Galaxy S24 Ultra')->first();
        if ($samsung) {
            ProductVariant::create([
                'product_id' => $samsung->product_id,
                'variant_name' => '256GB - Titanium Gray',
                'additional_price' => 0.00,
                'stock_quantity' => 12,
            ]);
            ProductVariant::create([
                'product_id' => $samsung->product_id,
                'variant_name' => '512GB - Titanium Black',
                'additional_price' => 120.00,
                'stock_quantity' => 10,
            ]);
            ProductVariant::create([
                'product_id' => $samsung->product_id,
                'variant_name' => '1TB - Titanium Violet',
                'additional_price' => 240.00,
                'stock_quantity' => 8,
            ]);
        }

        // MacBook Pro variants
        $macbook = Product::where('name', 'MacBook Pro 16"')->first();
        if ($macbook) {
            ProductVariant::create([
                'product_id' => $macbook->product_id,
                'variant_name' => 'M3 Pro - 18GB RAM - 512GB SSD',
                'additional_price' => 0.00,
                'stock_quantity' => 8,
            ]);
            ProductVariant::create([
                'product_id' => $macbook->product_id,
                'variant_name' => 'M3 Pro - 36GB RAM - 1TB SSD',
                'additional_price' => 400.00,
                'stock_quantity' => 7,
            ]);
            ProductVariant::create([
                'product_id' => $macbook->product_id,
                'variant_name' => 'M3 Max - 48GB RAM - 1TB SSD',
                'additional_price' => 800.00,
                'stock_quantity' => 5,
            ]);
        }

        // Apple Watch variants
        $appleWatch = Product::where('name', 'Apple Watch Series 9')->first();
        if ($appleWatch) {
            ProductVariant::create([
                'product_id' => $appleWatch->product_id,
                'variant_name' => '41mm - Pink Aluminum',
                'additional_price' => 0.00,
                'stock_quantity' => 25,
            ]);
            ProductVariant::create([
                'product_id' => $appleWatch->product_id,
                'variant_name' => '45mm - Midnight Aluminum',
                'additional_price' => 30.00,
                'stock_quantity' => 30,
            ]);
            ProductVariant::create([
                'product_id' => $appleWatch->product_id,
                'variant_name' => '41mm - Silver Stainless Steel',
                'additional_price' => 300.00,
                'stock_quantity' => 15,
            ]);
            ProductVariant::create([
                'product_id' => $appleWatch->product_id,
                'variant_name' => '45mm - Graphite Stainless Steel',
                'additional_price' => 330.00,
                'stock_quantity' => 5,
            ]);
        }

        // AirPods Pro variants
        $airpods = Product::where('name', 'AirPods Pro (2nd Gen)')->first();
        if ($airpods) {
            ProductVariant::create([
                'product_id' => $airpods->product_id,
                'variant_name' => 'Lightning Charging Case',
                'additional_price' => 0.00,
                'stock_quantity' => 50,
            ]);
            ProductVariant::create([
                'product_id' => $airpods->product_id,
                'variant_name' => 'MagSafe Charging Case',
                'additional_price' => 0.00,
                'stock_quantity' => 50,
            ]);
        }

        // PlayStation 5 variants
        $ps5 = Product::where('name', 'PlayStation 5')->first();
        if ($ps5) {
            ProductVariant::create([
                'product_id' => $ps5->product_id,
                'variant_name' => 'Standard Edition',
                'additional_price' => 0.00,
                'stock_quantity' => 6,
            ]);
            ProductVariant::create([
                'product_id' => $ps5->product_id,
                'variant_name' => 'Digital Edition',
                'additional_price' => -100.00,
                'stock_quantity' => 4,
            ]);
        }
    }
}