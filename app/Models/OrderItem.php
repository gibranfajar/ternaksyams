<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function variantSize()
    {
        return $this->belongsTo(ProductVariantSize::class, 'product_variant_size_id');
    }
}
