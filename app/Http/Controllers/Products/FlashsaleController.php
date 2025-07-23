<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\ProductFlashSale;
use App\Models\ProductVariant;
use App\Models\ProductVariantSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FlashsaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $flashSales = FlashSale::with([
            'productFlashSales.variantSize.variant' => function ($q) {
                $q->with(['product', 'flavour']);
            },
            'productFlashSales.variantSize.size'
        ])->get();

        return view('products.flashsales', compact('flashSales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $variants = ProductVariant::with([
            'product',
            'flavour',
            'sizes.size'
        ])->get();

        // dd($variants);
        return view('products.createflashsale', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $variants = array_filter($request->input('variants', []), fn($v) => !empty($v['checked']));
            $request->merge(['variants' => array_values($variants)]); // Reset index

            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'variants' => 'required|array|min:1',
                'variants.*.id' => 'required|exists:product_variants,id',
                'variants.*.sizes' => 'required|array|min:1',
                'variants.*.sizes.*.size_id' => 'required|exists:product_variant_sizes,id',
                'variants.*.sizes.*.qty' => 'required|integer|min:1',
                'variants.*.sizes.*.price' => 'required|numeric|min:0',
                'variants.*.sizes.*.discount' => 'nullable|numeric|min:0|max:100',
            ]);

            DB::beginTransaction();

            $flashSale = FlashSale::create([
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            foreach ($validated['variants'] as $variant) {
                foreach ($variant['sizes'] as $size) {
                    $variantSize = ProductVariantSize::findOrFail($size['size_id']);

                    if ($size['qty'] > $variantSize->quantity) {
                        throw new \Exception("Stok flash sale melebihi stok tersedia untuk size ID {$size['size_id']}");
                    }

                    $variantSize->quantity -= $size['qty'];
                    $variantSize->save();

                    ProductFlashSale::create([
                        'product_variant_size_id' => $size['size_id'],
                        'price' => $size['price'],
                        'discount' => $size['discount'] ?? 0,
                        'quantity' => $size['qty'],
                        'flash_sale_id' => $flashSale->id,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('flashsales.index')->with('success', 'Flash Sale berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('FlashSale Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
