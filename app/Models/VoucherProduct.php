<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherProduct extends Model
{
    protected $table = 'voucher_products';
    protected $guarded = [];

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
