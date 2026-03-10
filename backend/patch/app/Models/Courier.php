<?php

namespace App\Models;

use App\Domain\Shared\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Courier extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = ['name','phone','active'];
    protected $casts = ['active' => 'bool'];

    public function ratings(): HasMany
    {
        return $this->hasMany(CourierRating::class);
    }
}
