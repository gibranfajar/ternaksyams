<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingOption extends Model
{
    protected $table = 'shipping_options';
    protected $guarded = [];

    public function shippings()
    {
        return $this->hasMany(Shipping::class);
    }
}
