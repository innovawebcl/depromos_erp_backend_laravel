<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryAddress extends Model
{
    protected $fillable = [
        'order_id',
        'street',
        'number',
        'apartment',
        'city',
        'region',
        'postal_code',
        'latitude',
        'longitude',
        'instructions',
        'contact_name',
        'contact_phone',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
