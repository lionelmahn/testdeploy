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
        // 1. Thêm các cột thiếu vào bảng orders
        Schema::table('orders', function (Blueprint $table) {
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

        // 2. Thêm các cột thiếu vào bảng order_items
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'product_id')) {
                $table->unsignedBigInteger('product_id')->after('order_id');
            }
            if (!Schema::hasColumn('order_items', 'product_name')) {
                $table->string('product_name')->after('product_id');
            }
            if (!Schema::hasColumn('order_items', 'variant_name')) {
                $table->string('variant_name')->after('variant_id');
            }
            if (!Schema::hasColumn('order_items', 'total')) {
                $table->decimal('total', 12, 2)->after('price');
            }
        });

        // 3. Cập nhật dữ liệu cho các đơn hàng hiện có
        $this->updateExistingOrders();
        $this->updateExistingOrderItems();
    }

    /**
     * Cập nhật dữ liệu cho các đơn hàng hiện có
     */
    private function updateExistingOrders()
    {
        $orders = DB::table('orders')->get();
        
        foreach ($orders as $order) {
            $updateData = [];
            
            // Tạo order_number nếu chưa có
            if (empty($order->order_number)) {
                $updateData['order_number'] = 'TM' . date('Ymd', strtotime($order->order_date)) . str_pad($order->order_id, 4, '0', STR_PAD_LEFT);
            }
            
            // Set payment method mặc định
            if (empty($order->payment_method)) {
                $updateData['payment_method'] = 'cod';
            }
            
            // Set payment status mặc định
            if (empty($order->payment_status)) {
                $updateData['payment_status'] = $order->status === 'delivered' ? 'paid' : 'pending';
            }
            
            // Lấy thông tin user để điền shipping info
            $user = DB::table('users')->where('id', $order->user_id)->first();
            
            if (empty($order->shipping_name) && $user) {
                $updateData['shipping_name'] = $user->name;
                $updateData['shipping_phone'] = $user->phone ?? '0123456789';
                $updateData['shipping_city'] = 'Hà Nội';
                $updateData['shipping_district'] = 'Quận Ba Đình';
                $updateData['shipping_ward'] = 'Phường Phúc Xá';
            }
            
            // Tính toán lại các khoản phí từ order_items
            $orderItems = DB::table('order_items')->where('order_id', $order->order_id)->get();
            $subtotal = 0;
            
            foreach ($orderItems as $item) {
                $itemTotal = $item->price * $item->quantity;
                $subtotal += $itemTotal;
                
                // Cập nhật total cho order_item nếu chưa có
                if (empty($item->total)) {
                    DB::table('order_items')
                        ->where('order_item_id', $item->order_item_id)
                        ->update(['total' => $itemTotal]);
                }
            }
            
            if (empty($order->subtotal)) {
                $shipping_fee = $subtotal >= 500000 ? 0 : 30000;
                $tax_amount = $subtotal * 0.1;
                
                $updateData['subtotal'] = $subtotal;
                $updateData['shipping_fee'] = $shipping_fee;
                $updateData['tax_amount'] = $tax_amount;
                $updateData['total_amount'] = $subtotal + $shipping_fee + $tax_amount;
            }
            
            // Cập nhật nếu có dữ liệu cần update
            if (!empty($updateData)) {
                DB::table('orders')
                    ->where('order_id', $order->order_id)
                    ->update($updateData);
            }
        }
    }

    /**
     * Cập nhật dữ liệu cho các order_items hiện có
     */
    private function updateExistingOrderItems()
    {
        $orderItems = DB::table('order_items')->get();
        
        foreach ($orderItems as $item) {
            $updateData = [];
            
            // Lấy thông tin variant và product
            $variant = DB::table('product_variants')->where('variant_id', $item->variant_id)->first();
            
            if ($variant) {
                $product = DB::table('products')->where('product_id', $variant->product_id)->first();
                
                if ($product) {
                    if (empty($item->product_id)) {
                        $updateData['product_id'] = $product->product_id;
                    }
                    
                    if (empty($item->product_name)) {
                        $updateData['product_name'] = $product->name;
                    }
                    
                    if (empty($item->variant_name)) {
                        // Tạo tên variant chi tiết
                        $variantDetails = [];
                        if (!empty($variant->color)) $variantDetails[] = "Màu: " . $variant->color;
                        if (!empty($variant->size)) $variantDetails[] = "Size: " . $variant->size;
                        if (!empty($variant->storage)) $variantDetails[] = "Bộ nhớ: " . $variant->storage;
                        
                        $updateData['variant_name'] = !empty($variantDetails) ? 
                            implode(', ', $variantDetails) : 
                            ($variant->name ?? 'Phiên bản mặc định');
                    }
                    
                    if (empty($item->total)) {
                        $updateData['total'] = $item->price * $item->quantity;
                    }
                    
                    // Cập nhật nếu có dữ liệu cần update
                    if (!empty($updateData)) {
                        DB::table('order_items')
                            ->where('order_item_id', $item->order_item_id)
                            ->update($updateData);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'order_number', 'payment_method', 'payment_status', 
                'shipping_name', 'shipping_phone', 'shipping_city', 
                'shipping_district', 'shipping_ward', 'subtotal', 
                'shipping_fee', 'tax_amount', 'notes', 
                'shipped_at', 'delivered_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            $columns = ['product_id', 'product_name', 'variant_name', 'total'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('order_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
