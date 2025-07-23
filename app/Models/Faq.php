<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faqs';
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(FaqCategory::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
