
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleModulePermission extends Model
{
    protected $fillable = ['role_id','module_id','enabled'];
    protected $casts = ['enabled' => 'bool'];

    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
    public function module(): BelongsTo { return $this->belongsTo(Module::class); }
}
