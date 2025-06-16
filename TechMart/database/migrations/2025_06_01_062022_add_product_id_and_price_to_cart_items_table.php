<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Thêm cột product_id
            $table->unsignedBigInteger('product_id')->after('user_id');

            // Thêm cột price
            $table->decimal('price', 10, 2)->after('quantity');

            // Cho phép variant_id nullable (vì sản phẩm có thể không có variant)
            $table->unsignedBigInteger('variant_id')->nullable()->change();

            // Thêm foreign key constraints
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Xóa foreign key trước
            $table->dropForeign(['product_id']);

            // Xóa các cột
            $table->dropColumn(['product_id', 'price']);

            // Đặt lại variant_id không nullable
            $table->unsignedBigInteger('variant_id')->nullable(false)->change();
        });
    }
};
