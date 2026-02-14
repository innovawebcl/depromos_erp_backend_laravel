
<?php

namespace App\Application\Tariffs;

use App\Domain\Common\Result;
use App\Models\Commune;
use App\Models\CommuneTariff;
use Illuminate\Support\Facades\DB;

class SetCommuneTariffUseCase
{
    public function execute(int $communeId, float $amount, bool $active = true): Result
    {
        $commune = Commune::query()->find($communeId);
        if (!$commune) return Result::fail('Comuna no encontrada', 404);

        return DB::transaction(function () use ($communeId, $amount, $active) {
            // cierra tarifas anteriores activas
            CommuneTariff::query()
                ->where('commune_id', $communeId)
                ->where('active', true)
                ->update(['active' => false, 'ends_at' => now(), 'updated_at' => now()]);

            $tariff = CommuneTariff::create([
                'commune_id' => $communeId,
                'amount' => $amount,
                'active' => $active,
                'starts_at' => now(),
                'ends_at' => null,
            ]);

            return Result::ok($tariff);
        });
    }
}
