<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['key','name','active'];
    protected $casts = ['active' => 'bool'];
}
