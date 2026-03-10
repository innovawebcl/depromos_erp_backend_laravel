<?php

namespace App\Models;

use App\Domain\Shared\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = ['name','email','phone','purchase_goal','purchase_count','is_blacklisted'];
    protected $casts = ['purchase_goal' => 'int', 'purchase_count' => 'int', 'is_blacklisted' => 'bool'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
