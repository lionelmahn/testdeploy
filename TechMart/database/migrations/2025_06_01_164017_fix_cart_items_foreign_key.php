<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Xóa foreign key cũ
            $table->dropForeign(['user_id']);

            // Thêm foreign key mới
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Xóa foreign key mới
            $table->dropForeign(['user_id']);

            // Thêm lại foreign key cũ
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }
};
