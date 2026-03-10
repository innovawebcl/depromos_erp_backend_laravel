<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // H5: Índice en order_status_history.order_id
        // Acelera búsqueda de historial por pedido y JOINs
        Schema::table('order_status_history', function (Blueprint $table) {
            $table->index('order_id', 'idx_osh_order_id');
        });

        // H6: Índice compuesto en picking_scans (picking_session_id, order_item_id)
        // Acelera GROUP BY y SUM en ClosePickingUseCase y ScanItemUseCase
        Schema::table('picking_scans', function (Blueprint $table) {
            $table->index(['picking_session_id', 'order_item_id'], 'idx_ps_session_item');
        });

        // H7: Índice en courier_ratings.courier_id
        // Acelera withAvg('ratings','rating') en CourierController
        Schema::table('courier_ratings', function (Blueprint $table) {
            $table->index('courier_id', 'idx_cr_courier_id');
        });

        // H11: Índice compuesto en banners (active, starts_at, ends_at)
        // Acelera filtro de banners activos por rango de fechas
        Schema::table('banners', function (Blueprint $table) {
            $table->index(['active', 'starts_at', 'ends_at'], 'idx_banners_active_dates');
        });

        // H12: Índice en order_items.order_id (complementa FK)
        // Acelera carga de ítems por pedido
        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['order_id', 'product_id'], 'idx_oi_order_product');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_oi_order_product');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropIndex('idx_banners_active_dates');
        });

        Schema::table('courier_ratings', function (Blueprint $table) {
            $table->dropIndex('idx_cr_courier_id');
        });

        Schema::table('picking_scans', function (Blueprint $table) {
            $table->dropIndex('idx_ps_session_item');
        });

        Schema::table('order_status_history', function (Blueprint $table) {
            $table->dropIndex('idx_osh_order_id');
        });
    }
};
