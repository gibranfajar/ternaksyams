<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $table = 'tutorials';
    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany(TutorialCategory::class, 'tutorial_categories_link', 'tutorial_id', 'category_id')->withTimestamps();
    }
}
