<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'cart_items';
    protected $guarded = [];

    public function variantSize()
    {
        return $this->belongsTo(ProductVariantSize::class, 'product_variant_size_id');
    }
}
