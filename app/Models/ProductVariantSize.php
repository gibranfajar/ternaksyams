<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantSize extends Model
{
    protected $table = 'product_variant_sizes';
    protected $guarded = [];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function flashSales()
    {
        return $this->hasMany(ProductFlashSale::class, 'product_variant_size_id');
    }
}
