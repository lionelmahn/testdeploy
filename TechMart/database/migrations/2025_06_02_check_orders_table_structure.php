<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kiểm tra cấu trúc bảng orders
        $columns = Schema::getColumnListing('orders');
        Log::info('Orders table structure', ['columns' => $columns]);
        
        // Kiểm tra xem có cột id không
        if (!in_array('id', $columns) && in_array('order_id', $columns)) {
            // Cập nhật routes để sử dụng order_id thay vì id
            $routeFile = base_path('routes/web.php');
            $routeContent = file_get_contents($routeFile);
            
            // Thay thế route pattern nếu cần
            $updatedContent = str_replace(
                "Route::get('/checkout/success/{orderId}'",
                "Route::get('/checkout/success/{orderId}'",
                $routeContent
            );
            
            file_put_contents($routeFile, $updatedContent);
            
            Log::info('Routes updated to use order_id');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Không cần thực hiện gì trong down migration
    }
};
