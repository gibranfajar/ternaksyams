<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductVariantResource;
use App\Http\Resources\VariantProductResource;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getVariantsAll()
    {
        $products = Product::with([
            'variants.flavour',
            'variants.images' => function ($q) {
                $q->orderBy('sort', 'desc');
            },
            'variants.sizes.size'
        ])->get();

        $data = [];

        foreach ($products as $index => $product) {
            $variantList = [];
            $sizeList = [];

            foreach ($product->variants as $variant) {
                // Ambil gambar pertama berdasarkan sort desc
                $image = $variant->images->first();

                $variantList[] = [
                    'flavour' => $variant->flavour->name ?? '-',
                    'image' => $image ? $image->image : null,
                ];

                foreach ($variant->sizes as $vs) {
                    $sizeList[] = [
                        'label' => $vs->size->label . ' ' . $vs->size->unit,
                        'stock' => $vs->quantity,
                        'price' => (float) $vs->price,
                        'discount_percent' => $vs->discount,
                        'discount_value' => (float) $vs->discount_price,
                    ];
                }
            }

            if (count($variantList)) {
                $data[] = [
                    'id' => $index + 1,
                    'product' => $product->name,
                    'description' => $product->description,
                    'benefit' => $product->benefit,
                    'variants' => $variantList,
                    'sizes' => $sizeList,
                ];
            }
        }

        return response()->json([
            'data' => $data
        ]);
    }

    public function getProducts()
    {
        $products = Product::with([
            'variants.flavour',
            'variants.images' => fn($q) => $q->orderBy('sort', 'desc'),
            'variants.sizes.flashSales.flashSale',
            'variants.categories',
        ])->get();

        $data = [];

        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                $image = $variant->images->first()?->image ?? null;
                $lowestFlashSale = null;
                $usedSize = null;

                foreach ($variant->sizes as $size) {
                    $flash = collect($size->flashSales)->filter(function ($fs) {
                        return $fs->flashSale &&
                            $fs->flashSale->start_date <= now() &&
                            $fs->flashSale->end_date >= now();
                    })->sortBy('price')->first();

                    if ($flash && ($lowestFlashSale === null || $flash->price < $lowestFlashSale->price)) {
                        $lowestFlashSale = $flash;
                        $usedSize = $size;
                    }
                }

                if (!$lowestFlashSale) {
                    $usedSize = $variant->sizes->sortBy('price')->first();
                }

                $data[] = [
                    'id' => $variant->id,
                    'product' => $product->name . ' - ' . ($variant->flavour->name ?? '-'),
                    'slug' => $product->slug,
                    'flavour' => $variant->flavour->name ?? '-',
                    'price' => $usedSize?->price,
                    'discount' => $lowestFlashSale
                        ? (float) $lowestFlashSale->discount
                        : $usedSize?->discount,
                    'price_discount' => $lowestFlashSale
                        ? (float) $lowestFlashSale->price
                        : $usedSize?->discount_price,
                    'image' => $image,
                    'category' => $variant->categories->pluck('name')->toArray(),
                    'is_flash_sale' => $lowestFlashSale !== null
                ];
            }
        }

        return response()->json([
            'data' => $data
        ]);
    }


    // detail get product
    public function getProductDetail($slug)
    {
        $product = Product::with([
            'variants.flavour',
            'variants.images' => fn($q) => $q->orderBy('sort', 'desc'),
            'variants.sizes.flashSales.flashSale', // tambahkan flash sale relasi
            'variants.sizes.size',
            'variants.categories'
        ])->where('slug', $slug)->firstOrFail();

        $now = now();

        // Ambil semua kategori dari product (melalui variant → categories)
        $categoryIds = $product->variants->flatMap(function ($variant) {
            return $variant->categories->pluck('id');
        })->unique()->values();

        // Query produk terkait dengan kategori yang sama
        $relatedProducts = Product::whereHas('variants.categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })
            ->where('id', '!=', $product->id)
            ->with([
                'variants.images' => fn($q) => $q->orderBy('sort', 'desc'),
                'variants.sizes.size'
            ])
            ->limit(4)
            ->get();

        $data = [
            "id" => $product->id,
            "name" => $product->name,
            "description" => $product->description,
            "benefit" => $product->benefit,
            "nutrition" => $product->nutrition,
            "variants" => $product->variants->map(function ($variant) use ($now) {
                return [
                    "id" => $variant->id,
                    "name" => $variant->flavour->name ?? 'Default',
                    "images" => $variant->images->map(function ($image) {
                        return [
                            "id" => $image->id,
                            "image" => $image->image,
                            "sort" => $image->sort
                        ];
                    }),
                    "sizes" => $variant->sizes->map(function ($variantSize) use ($now) {
                        $activeFlash = $variantSize->flashSales->filter(function ($fs) use ($now) {
                            return $fs->flashSale &&
                                $fs->flashSale->start_date <= $now &&
                                $fs->flashSale->end_date >= $now;
                        })->sortBy('price')->first();

                        if ($activeFlash) {
                            return [
                                "id" => $variantSize->id,
                                "label" => $variantSize->size->label ?? '-',
                                "price" => (int) $variantSize->price,
                                "discount" => (int) $activeFlash->discount,
                                "price_discount" => (int) $activeFlash->price,
                                "quantity" => (int) $variantSize->quantity,
                                "is_flash_sale" => true
                            ];
                        }

                        return [
                            "id" => $variantSize->id,
                            "label" => $variantSize->size->label ?? '-',
                            "price" => (int) $variantSize->price,
                            "discount" => (int) $variantSize->discount,
                            "price_discount" => (int) $variantSize->discount_price,
                            "quantity" => (int) $variantSize->quantity,
                            "is_flash_sale" => false
                        ];
                    })
                ];
            }),
            "related_products" => $relatedProducts->map(function ($related) {
                $variant = $related->variants->first();

                return [
                    "id" => $related->id,
                    "name" => $related->name,
                    "thumbnail" => $variant?->images?->first()?->image ?? null,
                    "price" => $variant?->sizes?->first()?->price ?? null,
                    "discount" => $variant?->sizes?->first()?->discount ?? null,
                    "price_discount" => $variant?->sizes?->first()?->discount_price ?? null
                ];
            })
        ];

        return response()->json([
            "data" => $data
        ]);
    }


    public function getProductFlashSale()
    {
        $now = now();

        $flashSales = FlashSale::with([
            'productFlashSales.variantSize.variant.flavour',
            'productFlashSales.variantSize.variant.product',
            'productFlashSales.variantSize.variant.images' => fn($q) => $q->orderBy('sort', 'desc'),
            'productFlashSales.variantSize.variant.categories',
            'productFlashSales.variantSize.size'
        ])
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        $data = [];

        foreach ($flashSales as $flashSale) {
            // Group items by variant_id
            $grouped = $flashSale->productFlashSales->groupBy(function ($item) {
                return $item->variantSize->variant->id ?? null;
            });

            foreach ($grouped as $variantId => $items) {
                // Ambil satu dengan size terkecil (label atau size_id)
                $selected = $items->sortBy(function ($item) {
                    return $item->variantSize->size->label ?? $item->variantSize->size_id;
                })->first();

                if (!$selected) continue;

                $variantSize = $selected->variantSize;
                $variant = $variantSize->variant;
                $product = $variant->product;
                $image = $variant->images->first()?->image;
                $size = $variantSize->size;

                $data[] = [
                    'id' => $variant->id,
                    'product' => $product->name . ' - ' . ($variant->flavour->name ?? '-'),
                    'slug' => $product->slug,
                    'flavour' => $variant->flavour->name ?? '-',
                    'size' => $size->label . ' ' . $size->unit,
                    'original_price' => (float) $variantSize->price,
                    'discount' => (float) $selected->discount,
                    'price_discount' => (float) $selected->price,
                    'quantity' => (int) $selected->quantity,
                    'image' => $image,
                    'category' => $variant->categories->pluck('name')->toArray(),
                    'flash_sale_start' => $flashSale->start_date,
                    'flash_sale_end' => $flashSale->end_date,
                    'is_flash_sale' => true,
                ];
            }
        }

        return response()->json([
            'data' => $data
        ]);
    }
}
