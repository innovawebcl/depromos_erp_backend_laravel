<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommuneTariff extends Model
{
    protected $fillable = ['commune_id','amount','active','starts_at','ends_at'];
    protected $casts = ['amount' => 'decimal:2', 'active' => 'bool', 'starts_at' => 'datetime', 'ends_at' => 'datetime'];

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }
}
