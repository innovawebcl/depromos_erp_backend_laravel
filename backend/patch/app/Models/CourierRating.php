
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourierRating extends Model
{
    protected $fillable = ['courier_id','rating','comment'];
    protected $casts = ['rating' => 'int'];

    public function courier(): BelongsTo
    {
        return $this->belongsTo(Courier::class);
    }
}
