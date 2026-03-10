<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickingScan extends Model
{
    protected $fillable = ['picking_session_id','order_item_id','scanned_code','scanned_quantity'];
    protected $casts = ['scanned_quantity' => 'int'];

    public function session(): BelongsTo { return $this->belongsTo(PickingSession::class, 'picking_session_id'); }
    public function orderItem(): BelongsTo { return $this->belongsTo(OrderItem::class); }
}
