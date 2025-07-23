<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function flavour()
    {
        return $this->belongsTo(Flavour::class, 'flavour_id');
    }


    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_variant_id')->orderby('sort', 'desc');
    }

    public function product_variant_sizes()
    {
        return $this->hasMany(ProductVariantSize::class);
    }

    public function sizes()
    {
        return $this->hasMany(ProductVariantSize::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function flashSales()
    {
        return $this->hasMany(ProductFlashSale::class, 'product_variant_size_id');
    }
}
