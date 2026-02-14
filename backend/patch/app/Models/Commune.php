
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends Model
{
    protected $fillable = ['name','active'];
    protected $casts = ['active' => 'bool'];

    public function tariffs(): HasMany
    {
        return $this->hasMany(CommuneTariff::class);
    }
}
