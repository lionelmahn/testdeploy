<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Thêm cột product_id nếu chưa có
            if (!Schema::hasColumn('order_items', 'product_id')) {
                $table->unsignedBigInteger('product_id')->after('order_id');
            }
            
            // Thêm cột product_name nếu chưa có
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->after('product_id')->nullable();
            }
            
            // Thêm cột variant_name nếu chưa có
            if (!Schema::hasColumn('order_items', 'variant_name')) {
                $table->string('variant_name')->after('variant_id')->nullable();
            }
            
            // Thêm cột total nếu chưa có
            if (!Schema::hasColumn('order_items', 'total')) {
                $table->decimal('total', 12, 2)->after('price')->nullable();
            }
            
            // Thêm foreign key cho product_id
            if (!Schema::hasColumn('order_items', 'product_id')) {
                $table->foreign('product_id')->references('product_id')->on('products')->onDelete('set null');
            }
        });
        
        // Cập nhật dữ liệu cho các đơn hàng hiện có
        $this->updateExistingOrderItems();
    }
    
    /**
     * Cập nhật dữ liệu cho các order_items hiện có
     */
    private function updateExistingOrderItems()
    {
        // Lấy tất cả order items
        $orderItems = DB::table('order_items')->get();
        
        foreach ($orderItems as $item) {
            // Tìm variant tương ứng
            $variant = DB::table('product_variants')
                ->where('variant_id', $item->variant_id)
                ->first();
            
            if ($variant) {
                // Tìm product tương ứng
                $product = DB::table('products')
                    ->where('product_id', $variant->product_id)
                    ->first();
                
                if ($product) {
                    // Cập nhật order_item
                    DB::table('order_items')
                        ->where('order_item_id', $item->order_item_id)
                        ->update([
                            'product_id' => $product->product_id,
                            'product_name' => $product->name,
                            'variant_name' => $variant->name ?? 'Mặc định',
                            'total' => $item->price * $item->quantity
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Xóa các cột đã thêm
            $columns = ['product_name', 'variant_name', 'total'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
            
            // Xóa foreign key và cột product_id
            if (Schema::hasColumn('order_items', 'product_id')) {
                $table->dropForeign(['product_id']);
                $table->dropColumn('product_id');
            }
        });
    }
};
