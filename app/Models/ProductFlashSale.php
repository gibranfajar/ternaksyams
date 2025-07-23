<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFlashSale extends Model
{
    protected $table = 'product_flash_sales';
    protected $guarded = [];

    public function variantSize()
    {
        return $this->belongsTo(ProductVariantSize::class, 'product_variant_size_id');
    }

    public function flashSale()
    {
        return $this->belongsTo(FlashSale::class);
    }
}
