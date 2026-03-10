<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // H10: Agregar unique constraint a customers.email
        // Nota: si existen duplicados, esta migración fallará.
        // En ese caso, primero limpiar duplicados manualmente.
        Schema::table('customers', function (Blueprint $table) {
            // Primero eliminar el index existente (si existe) para reemplazarlo por unique
            $table->dropIndex(['email']);
            $table->unique('email', 'customers_email_unique');
        });

        // H13: Corregir commune_tariffs.starts_at para usar useCurrent() en vez de DB::raw
        // No se puede alterar el default en una migración de forma portable,
        // pero documentamos la intención. El fix real se aplica en la migración original
        // si se recrea la BD. Para BDs existentes, el default ya está seteado.
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique('customers_email_unique');
            $table->index('email');
        });
    }
};
