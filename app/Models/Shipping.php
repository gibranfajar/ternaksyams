<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $table = 'shippings';
    protected $guarded = [];

    public function shippingOption()
    {
        return $this->belongsTo(ShippingOption::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
