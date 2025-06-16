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
        Schema::table('orders', function (Blueprint $table) {
            // Kiểm tra và thêm các cột còn thiếu
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number')->unique()->after('user_id');
            }
            
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->enum('payment_method', ['cod', 'bank_transfer', 'momo', 'vnpay'])->after('status');
            }
            
            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('payment_method');
            }
            
            if (!Schema::hasColumn('orders', 'shipping_name')) {
                $table->string('shipping_name')->after('payment_status');
            }
            
            if (!Schema::hasColumn('orders', 'shipping_phone')) {
                $table->string('shipping_phone')->after('shipping_name');
            }
            
            if (!Schema::hasColumn('orders', 'shipping_city')) {
                $table->string('shipping_city')->after('shipping_address');
            }
            
            if (!Schema::hasColumn('orders', 'shipping_district')) {
                $table->string('shipping_district')->after('shipping_city');
            }
            
            if (!Schema::hasColumn('orders', 'shipping_ward')) {
                $table->string('shipping_ward')->after('shipping_district');
            }
            
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->decimal('subtotal', 12, 2)->after('shipping_ward');
            }
            
            if (!Schema::hasColumn('orders', 'shipping_fee')) {
                $table->decimal('shipping_fee', 10, 2)->default(0)->after('subtotal');
            }
            
            if (!Schema::hasColumn('orders', 'tax_amount')) {
                $table->decimal('tax_amount', 10, 2)->default(0)->after('shipping_fee');
            }
            
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable()->after('total_amount');
            }
            
            if (!Schema::hasColumn('orders', 'shipped_at')) {
                $table->timestamp('shipped_at')->nullable()->after('notes');
            }
            
            if (!Schema::hasColumn('orders', 'delivered_at')) {
                $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columnsToCheck = [
                'order_number', 'payment_method', 'payment_status', 
                'shipping_name', 'shipping_phone', 'shipping_city', 
                'shipping_district', 'shipping_ward', 'subtotal', 
                'shipping_fee', 'tax_amount', 'notes', 
                'shipped_at', 'delivered_at'
            ];
            
            foreach ($columnsToCheck as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
