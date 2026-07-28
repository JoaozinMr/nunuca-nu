<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 12)->unique();
            $table->string('customer_name', 120);
            $table->string('customer_phone', 30);
            $table->string('customer_email', 120)->nullable();
            $table->enum('delivery_type', ['delivery', 'pickup']);
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', [
                'pending',
                'preparing',
                'out_for_delivery',
                'completed',
                'canceled',
            ])->default('pending');
            $table->decimal('total', 10, 2);
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
