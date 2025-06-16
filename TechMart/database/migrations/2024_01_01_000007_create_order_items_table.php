<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->string('product_name'); // Lưu tên sản phẩm tại thời điểm đặt hàng
            $table->string('variant_name')->nullable(); // Lưu tên variant tại thời điểm đặt hàng
            $table->integer('quantity');
            $table->decimal('price', 10, 2); // Giá tại thời điểm đặt hàng
            $table->decimal('total', 12, 2); // Tổng tiền = price * quantity
            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
