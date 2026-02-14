
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PickingSession extends Model
{
    protected $fillable = ['order_id','status'];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function scans(): HasMany { return $this->hasMany(PickingScan::class); }
}
