<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $guarded = [];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
