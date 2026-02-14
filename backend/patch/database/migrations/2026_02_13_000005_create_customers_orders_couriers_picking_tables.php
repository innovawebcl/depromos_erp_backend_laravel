
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable();
            $table->unsignedInteger('purchase_goal')->default(0); // meta para subir a Platinum
            $table->unsignedInteger('purchase_count')->default(0);
            $table->boolean('is_blacklisted')->default(false);
            $table->timestamps();
        });

        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('courier_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_id')->constrained('couriers')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1..5
            $table->string('comment')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('commune_id')->constrained('communes')->restrictOnDelete();
            $table->foreignId('courier_id')->nullable()->constrained('couriers')->nullOnDelete();
            $table->string('status')->default('pending'); // pending, picking, ready, en_route, delivered, cancelled
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->unsignedInteger('eta_minutes')->nullable(); // tiempo configurable al pasar a en_route
            $table->string('receiver_rut')->nullable(); // RUT receptor al cierre
            $table->string('delivery_photo_url')->nullable(); // foto de respaldo
            $table->timestamps();

            $table->index(['status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('product_size_id')->constrained('product_sizes')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->timestamps();
        });

        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->foreignId('changed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Picking
        Schema::create('picking_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->cascadeOnDelete();
            $table->string('status')->default('open'); // open, closed
            $table->timestamps();
        });

        Schema::create('picking_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('picking_session_id')->constrained('picking_sessions')->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->string('scanned_code'); // código escaneado (producto o etiqueta)
            $table->unsignedInteger('scanned_quantity')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('picking_scans');
        Schema::dropIfExists('picking_sessions');
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('courier_ratings');
        Schema::dropIfExists('couriers');
        Schema::dropIfExists('customers');
    }
};
