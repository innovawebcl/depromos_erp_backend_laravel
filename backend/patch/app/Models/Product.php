<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['code','name','description','price','photo_url','active'];
    protected $casts = ['price' => 'decimal:2', 'active' => 'bool'];

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }
}
