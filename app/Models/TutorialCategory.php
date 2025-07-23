<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TutorialCategory extends Model
{
    protected $table = 'tutorial_categories';
    protected $guarded = [];

    public function tutorials()
    {
        return $this->belongsToMany(Tutorial::class, 'tutorial_categories_link', 'category_id', 'tutorial_id')->withTimestamps();
    }
}
