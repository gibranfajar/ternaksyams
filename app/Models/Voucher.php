<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $table = 'vouchers';
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'voucher_users', 'voucher_id', 'user_id');
    }

    public function content()
    {
        return $this->hasOne(VoucherContent::class);
    }

    public function products()
    {
        return $this->hasMany(VoucherProduct::class, 'voucher_id');
    }
}
