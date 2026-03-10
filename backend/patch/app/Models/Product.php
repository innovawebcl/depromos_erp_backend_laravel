<?php

namespace App\Models;

use App\Domain\Shared\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = ['code','name','description','price','photo_url','active'];
    protected $casts = ['price' => 'decimal:2', 'active' => 'bool'];

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }
}
