
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Courier extends Model
{
    protected $fillable = ['name','phone','active'];
    protected $casts = ['active' => 'bool'];

    public function ratings(): HasMany
    {
        return $this->hasMany(CourierRating::class);
    }
}
