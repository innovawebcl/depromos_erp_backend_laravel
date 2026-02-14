
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id','product_id','product_size_id','quantity','unit_price'];
    protected $casts = ['quantity' => 'int', 'unit_price' => 'decimal:2'];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function size(): BelongsTo { return $this->belongsTo(ProductSize::class, 'product_size_id'); }
}
