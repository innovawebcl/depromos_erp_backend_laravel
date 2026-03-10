<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // código propio
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('photo_url')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('size'); // S, M, L, etc
            $table->boolean('active')->default(true);
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            $table->unique(['product_id','size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_sizes');
        Schema::dropIfExists('products');
    }
};
