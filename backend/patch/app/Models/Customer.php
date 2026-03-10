<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['name','email','phone','purchase_goal','purchase_count','is_blacklisted'];
    protected $casts = ['purchase_goal' => 'int', 'purchase_count' => 'int', 'is_blacklisted' => 'bool'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
