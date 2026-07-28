<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('flavor', 120);
            $table->string('category', 80);
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('image_path', 255)->nullable();
            $table->boolean('is_available')->default(true);
            $table->boolean('is_new')->default(false);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('min_stock')->default(5);
            $table->timestamps();

            $table->index('category');
            $table->index('is_available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
