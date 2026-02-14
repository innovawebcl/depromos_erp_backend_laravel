
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('commune_tariffs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->constrained('communes')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->boolean('active')->default(true);
            $table->dateTime('starts_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('ends_at')->nullable();
            $table->timestamps();

            $table->index(['commune_id','active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commune_tariffs');
        Schema::dropIfExists('communes');
    }
};
