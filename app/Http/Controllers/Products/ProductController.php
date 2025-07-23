<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Flavour;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantSize;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with([
            'variants.sizes.size',
            'variants.categories',
            'variants.flavour'
        ])->get();

        return view('products.products', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $flavours = Flavour::all();
        $categories = Category::all();
        $sizes = Size::all();
        return view('products.create', compact('flavours', 'categories', 'sizes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* ---------- 1. VALIDASI ---------- */
        $data = $request->validate([
            'product_name'             => 'required|string|max:255',
            'description'              => 'nullable|string',
            'benefit'                  => 'nullable|string',
            'status'                   => 'required|in:active,inactive',
            'product_nutrition'        => 'nullable|image|max:2048',

            'variants'                 => 'required|array|min:1',
            'variants.*.flavor'        => 'required|integer|exists:flavours,id',
            'variants.*.images.*'      => 'nullable|image|max:2048',
            'variants.*.categories'    => 'nullable|array',
            'variants.*.categories.*'  => 'integer|exists:categories,id',

            'variants.*.sizes'                 => 'required|array|min:1',
            'variants.*.sizes.*.size'          => 'required|integer|exists:sizes,id',
            'variants.*.sizes.*.stock'         => 'required|integer|min:0',
            'variants.*.sizes.*.price'         => 'required|numeric|min:0',
            'variants.*.sizes.*.discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            /* ---------- 2. SIMPAN PRODUCT ---------- */
            $slug = Str::slug($data['product_name']);
            if (Product::where('slug', $slug)->exists()) {
                $slug .= '-' . uniqid();
            }

            $product = Product::create([
                'name'        => $data['product_name'],
                'slug'        => $slug,
                'description' => $data['description'],
                'benefit'     => $data['benefit'],
                'nutrition'   => $request->hasFile('product_nutrition')
                    ? $request->file('product_nutrition')->store('nutritions', 'public')
                    : null,
            ]);

            /* ---------- 3. LOOP VARIANTS ---------- */
            foreach ($data['variants'] as $variantData) {

                $variant = $product->variants()->create([
                    'flavour_id' => $variantData['flavor'],
                    'status'     => $data['status'],
                ]);

                /* --- 3a. Categories (pivot) --- */
                if (!empty($variantData['categories'])) {
                    $variant->categories()->sync($variantData['categories']);
                }

                /* --- 3b. Images baru --- */
                if (!empty($variantData['images'])) {
                    $imagesPayload = [];
                    foreach ($variantData['images'] as $i => $img) {
                        $imagesPayload[] = [
                            'image' => $img->store('products/variants', 'public'),
                            'sort'  => $i,
                        ];
                    }
                    $variant->images()->createMany($imagesPayload);
                }

                /* --- 3c. Sizes --- */
                $sizesPayload = [];
                foreach ($variantData['sizes'] as $sizeData) {
                    $price   = $sizeData['price'];
                    $disc    = $sizeData['discount_percent'] ?? 0;
                    $final   = floor($price - ($price * $disc / 100));

                    $sizesPayload[] = [
                        'size_id'         => $sizeData['size'],
                        'quantity'        => $sizeData['stock'],
                        'price'           => $price,
                        'discount'        => $disc,
                        'discount_price'  => $final,
                    ];
                }
                $variant->sizes()->createMany($sizesPayload);
            }

            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product     = Product::with(['variants.categories', 'variants.images', 'variants.images' => fn($q) => $q->orderBy('sort', 'desc'), 'variants.sizes'])->findOrFail($id);

        $flavours    = Flavour::all();
        $sizes       = Size::all();
        $categories  = Category::all();

        return view('products.edit', compact(
            'product',
            'flavours',
            'sizes',
            'categories'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        /* ---------- 1. VALIDASI ---------- */
        $data = $request->validate([
            'product_name'                   => 'required|string|max:255',
            'description'                    => 'nullable|string',
            'benefit'                        => 'nullable|string',
            'status'                         => 'required|in:active,inactive',
            'product_nutrition'              => 'nullable|image|max:2048',

            'variants'                       => 'required|array|min:1',
            'variants.*.id'                  => 'nullable|integer|exists:product_variants,id',
            'variants.*._destroy'            => 'nullable|in:1',
            'variants.*.flavor'              => 'required|integer|exists:flavours,id',

            'variants.*.images'              => 'nullable|array',
            'variants.*.images.*'            => 'nullable|image|max:2048',

            'variants.*.categories'          => 'nullable|array',
            'variants.*.categories.*'        => 'integer|exists:categories,id',

            'variants.*.sizes'                      => 'required|array|min:1',
            'variants.*.sizes.*.id'                 => 'nullable|integer|exists:product_variant_sizes,id',
            'variants.*.sizes.*._destroy'           => 'nullable|in:1',
            'variants.*.sizes.*.size'               => 'required|integer|exists:sizes,id',
            'variants.*.sizes.*.price'              => 'required|numeric|min:0',
            'variants.*.sizes.*.discount_percent'   => 'nullable|numeric|min:0|max:100',
            'variants.*.sizes.*.stock'              => 'required|integer|min:0',
        ]);

        /* ---------- 2. TRANSAKSI ---------- */
        DB::transaction(function () use ($id, $data, $request) {

            /* ---------- 2a. UPDATE PRODUK ---------- */
            $product = Product::findOrFail($id);
            $product->update([
                'name'        => $data['product_name'],
                'description' => $data['description'],
                'benefit'     => $data['benefit'],
            ]);

            /* nutrition image */
            if ($request->hasFile('product_nutrition')) {
                if ($product->nutrition_image) {
                    Storage::disk('public')->delete($product->nutrition_image);
                }
                $product->nutrition_image = $request->file('product_nutrition')
                    ->store('products/nutrition', 'public');
                $product->save();
            }

            /* ---------- 2b. VARIANTS ---------- */
            foreach ($data['variants'] as $varData) {

                /* -- ❶ HAPUS VARIANT -- */
                if (!empty($varData['_destroy'])) {
                    if (!empty($varData['id'])) {
                        ProductVariant::find($varData['id'])?->delete();  // cascade sizes+images
                    }
                    continue;
                }

                /* -- ❷ SIMPAN / UPDATE VARIANT -- */
                $variant = !empty($varData['id'])
                    ? ProductVariant::find($varData['id'])
                    : $product->variants()->create([
                        'flavour_id' => $varData['flavor'],
                    ]);

                $variant->update([
                    'flavour_id' => $varData['flavor'],
                    'status'     => $data['status'],   // kalau status per‑produk, hapus baris ini
                ]);

                /* -- ❸ SYNC CATEGORIES -- */
                $variant->categories()->sync($varData['categories'] ?? []);

                /* -- ❹ GANTI GAMBAR VARIANT JIKA ADA YANG BARU -- */
                if (!empty($varData['images'])) {
                    // 1. Hapus gambar lama dari storage dan DB
                    foreach ($variant->images as $oldImg) {
                        if (Storage::disk('public')->exists($oldImg->image)) {
                            Storage::disk('public')->delete($oldImg->image);
                        }
                        $oldImg->delete();
                    }

                    // 2. Simpan gambar baru
                    foreach ($varData['images'] as $i => $img) {
                        $variant->images()->create([
                            'image' => $img->store('products/variants', 'public'),
                            'sort'       => $i,
                        ]);
                    }
                }


                /* -- ❺ SIZES -- */
                foreach ($varData['sizes'] as $szData) {

                    // hapus size
                    if (!empty($szData['_destroy'])) {
                        if (!empty($szData['id'])) {
                            ProductVariantSize::find($szData['id'])?->delete();
                        }
                        continue;
                    }

                    $price = $szData['price'];
                    $disc  = $szData['discount_percent'] ?? 0;
                    $final = floor($price - ($price * $disc / 100));

                    $sizeModel = !empty($szData['id'])
                        ? ProductVariantSize::find($szData['id'])
                        : $variant->sizes()->make();

                    $sizeModel->fill([
                        'size_id'        => $szData['size'],
                        'price'          => $price,
                        'discount'       => $disc,
                        'discount_price' => $final,
                        'quantity'       => $szData['stock'],
                    ])->save();
                }
            }
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function status(Request $request, string $id)
    {
        $variant = ProductVariant::findOrFail($id);
        $variant->update([
            'status' => $request->status,
        ]);
        return redirect()->route('products.index')->with('success', 'Status updated successfully');
    }
}
