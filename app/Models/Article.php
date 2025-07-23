<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    protected $guarded = [];

    public function categories()
    {
        return $this->belongsToMany(CategoryArticle::class, 'article_categories', 'article_id', 'category_id')->withTimestamps();
    }
}
