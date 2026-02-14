
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['title','image_url','target_url','starts_at','ends_at','active'];
    protected $casts = ['active' => 'bool', 'starts_at' => 'datetime', 'ends_at' => 'datetime'];
}
