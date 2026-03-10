<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = ['customer_id','commune_id','courier_id','status','delivery_fee','total','eta_minutes','receiver_rut','delivery_photo_url'];
    protected $casts = ['delivery_fee' => 'decimal:2', 'total' => 'decimal:2', 'eta_minutes' => 'int'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function commune(): BelongsTo { return $this->belongsTo(Commune::class); }
    public function courier(): BelongsTo { return $this->belongsTo(Courier::class); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function pickingSession(): HasOne { return $this->hasOne(PickingSession::class); }
    public function deliveryAddress(): HasOne { return $this->hasOne(DeliveryAddress::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function statusHistory(): HasMany { return $this->hasMany(OrderStatusHistory::class); }
}
