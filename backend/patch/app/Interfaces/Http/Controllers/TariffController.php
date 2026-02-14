
<?php

namespace App\Interfaces\Http\Controllers;

use App\Application\Tariffs\SetCommuneTariffUseCase;
use App\Models\Commune;
use App\Models\CommuneTariff;
use Illuminate\Http\Request;

class TariffController
{
    public function communes()
    {
        return response()->json(Commune::query()->with(['tariffs' => fn($q) => $q->orderByDesc('id')])->paginate(20));
    }

    public function toggleCommune(int $communeId)
    {
        $c = Commune::query()->findOrFail($communeId);
        $c->active = !$c->active;
        $c->save();
        return response()->json($c);
    }

    public function tariffHistory(int $communeId)
    {
        return response()->json(
            CommuneTariff::query()->where('commune_id', $communeId)->orderByDesc('id')->paginate(50)
        );
    }

    public function setCommuneTariff(int $communeId, Request $request, SetCommuneTariffUseCase $uc)
    {
        $payload = $request->validate([
            'amount' => ['required','numeric','min:0'],
            'active' => ['sometimes','boolean'],
        ]);

        $res = $uc->execute($communeId, (float)$payload['amount'], (bool)($payload['active'] ?? true));

        return $res->ok
            ? response()->json($res->data, 201)
            : response()->json(['message' => $res->error], $res->code);
    }
}
