<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // created, updated, deleted, restored
            $table->string('auditable_type'); // App\Models\Product, etc.
            $table->unsignedBigInteger('auditable_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            // Índices para búsqueda eficiente
            $table->index(['auditable_type', 'auditable_id'], 'idx_audit_auditable');
            $table->index('user_id', 'idx_audit_user');
            $table->index('created_at', 'idx_audit_created');
            $table->index('action', 'idx_audit_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
