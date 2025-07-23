<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partners';
    protected $guarded = [];

    public function accounts()
    {
        return $this->hasMany(PartnerAccount::class);
    }
}
