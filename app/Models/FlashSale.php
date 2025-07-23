<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSale extends Model
{
    protected $table = 'flash_sales';
    protected $guarded = [];

    public function productFlashSales()
    {
        return $this->hasMany(ProductFlashSale::class);
    }
}
