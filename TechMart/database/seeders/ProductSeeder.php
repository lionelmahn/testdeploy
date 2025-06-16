<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $smartphones = Category::where('category_name', 'Smartphones')->first();
        $laptops = Category::where('category_name', 'Laptops')->first();
        $tablets = Category::where('category_name', 'Tablets')->first();
        $audio = Category::where('category_name', 'Audio Devices')->first();
        $watches = Category::where('category_name', 'Smart Watches')->first();
        $gaming = Category::where('category_name', 'Gaming')->first();

        $products = [
            // Smartphones
            [
                'name' => 'iPhone 15 Pro',
                'description' => 'The latest iPhone with A17 Pro chip, titanium design, and advanced camera system.',
                'price' => 999.00,
                'stock_quantity' => 50,
                'category_id' => $smartphones->category_id,
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Premium Android smartphone with S Pen, 200MP camera, and AI features.',
                'price' => 1199.00,
                'stock_quantity' => 30,
                'category_id' => $smartphones->category_id,
            ],
            [
                'name' => 'Google Pixel 8 Pro',
                'description' => 'Google\'s flagship phone with advanced AI photography and pure Android experience.',
                'price' => 899.00,
                'stock_quantity' => 25,
                'category_id' => $smartphones->category_id,
            ],

            // Laptops
            [
                'name' => 'MacBook Pro 16"',
                'description' => 'Powerful laptop with M3 Pro chip, perfect for professionals and creators.',
                'price' => 2499.00,
                'stock_quantity' => 20,
                'category_id' => $laptops->category_id,
            ],
            [
                'name' => 'Dell XPS 13',
                'description' => 'Ultra-portable laptop with stunning InfinityEdge display and premium build quality.',
                'price' => 1299.00,
                'stock_quantity' => 15,
                'category_id' => $laptops->category_id,
            ],
            [
                'name' => 'ThinkPad X1 Carbon',
                'description' => 'Business laptop with legendary ThinkPad reliability and performance.',
                'price' => 1599.00,
                'stock_quantity' => 12,
                'category_id' => $laptops->category_id,
            ],

            // Tablets
            [
                'name' => 'iPad Pro 12.9"',
                'description' => 'The ultimate iPad experience with M2 chip and Liquid Retina XDR display.',
                'price' => 1099.00,
                'stock_quantity' => 35,
                'category_id' => $tablets->category_id,
            ],
            [
                'name' => 'Samsung Galaxy Tab S9+',
                'description' => 'Premium Android tablet with S Pen included and AMOLED display.',
                'price' => 799.00,
                'stock_quantity' => 20,
                'category_id' => $tablets->category_id,
            ],

            // Audio Devices
            [
                'name' => 'AirPods Pro (2nd Gen)',
                'description' => 'Premium wireless earbuds with active noise cancellation and spatial audio.',
                'price' => 249.00,
                'stock_quantity' => 100,
                'category_id' => $audio->category_id,
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => 'Industry-leading noise canceling headphones with exceptional sound quality.',
                'price' => 399.00,
                'stock_quantity' => 40,
                'category_id' => $audio->category_id,
            ],
            [
                'name' => 'Bose QuietComfort Earbuds',
                'description' => 'True wireless earbuds with world-class noise cancellation.',
                'price' => 279.00,
                'stock_quantity' => 60,
                'category_id' => $audio->category_id,
            ],

            // Smart Watches
            [
                'name' => 'Apple Watch Series 9',
                'description' => 'The most advanced Apple Watch with new S9 chip and Double Tap gesture.',
                'price' => 399.00,
                'stock_quantity' => 75,
                'category_id' => $watches->category_id,
            ],
            [
                'name' => 'Samsung Galaxy Watch 6',
                'description' => 'Advanced smartwatch with comprehensive health tracking and long battery life.',
                'price' => 329.00,
                'stock_quantity' => 45,
                'category_id' => $watches->category_id,
            ],

            // Gaming
            [
                'name' => 'PlayStation 5',
                'description' => 'Next-generation gaming console with ultra-high speed SSD and ray tracing.',
                'price' => 499.00,
                'stock_quantity' => 10,
                'category_id' => $gaming->category_id,
            ],
            [
                'name' => 'Xbox Series X',
                'description' => 'The fastest, most powerful Xbox ever with 4K gaming and Quick Resume.',
                'price' => 499.00,
                'stock_quantity' => 8,
                'category_id' => $gaming->category_id,
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'description' => 'Hybrid gaming console with vibrant OLED screen and versatile gameplay.',
                'price' => 349.00,
                'stock_quantity' => 25,
                'category_id' => $gaming->category_id,
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }
    }
}