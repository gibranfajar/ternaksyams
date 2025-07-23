<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryArticle extends Model
{
    protected $table = 'category_articles';
    protected $guarded = [];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_categories', 'category_id', 'article_id')->withTimestamps();
    }
}
