<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherContent;
use App\Models\VoucherProduct;
use App\Models\VoucherUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vouchers = Voucher::with([
            'users',
            'content',
            'products.productVariant.product',
            'products.productVariant.flavour'
        ])->get();
        return view('vouchers.index', compact('vouchers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = ProductVariant::with('product', 'flavour')->get();
        $users = User::with('profiles')->get();
        return view('vouchers.create', compact('products', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:vouchers,code',
            'type' => 'required|in:product,shipping,transaction',
            'amount_type' => 'required|in:fixed,percent',
            'amount' => 'required|numeric',
            'min_transaction' => 'required|numeric',
            'target_audience' => 'required|in:all,user,guest',
            'quota' => 'required|integer',
            'limit' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'max_discount' => 'nullable|numeric',
            'products' => 'nullable|array',
            'users' => 'nullable|array',
            'users.*' => 'integer|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            // 1. Simpan voucher utama
            $voucher = Voucher::create([
                'code' => strtoupper($request->code),
                'type' => $request->type,
                'amount_type' => $request->amount_type,
                'amount' => $request->amount,
                'max_discount' => $request->amount_type === 'percent' ? $request->max_discount : null,
                'min_transaction' => $request->min_transaction,
                'target_audience' => $request->target_audience,
                'quota' => $request->quota,
                'limit' => $request->limit,
                'used' => 0,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => true,
            ]);

            // 2. Jika type = product → simpan ke voucher_products
            if ($request->type === 'product' && $request->filled('products')) {
                foreach ($request->products as $productId) {
                    VoucherProduct::create([
                        'voucher_id' => $voucher->id,
                        'product_variant_id' => $productId,
                    ]);
                }
            }

            // 3. Jika target_audience = user → simpan ke voucher_contents
            if ($request->target_audience === 'user') {
                $bannerPath = null;
                if ($request->hasFile('banner')) {
                    $bannerPath = $request->file('banner')->store('voucher_banners', 'public');
                }

                VoucherContent::create([
                    'voucher_id' => $voucher->id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'banner' => $bannerPath,
                    'used_at' => $request->used_at,
                    'tnc' => $request->tnc,
                ]);

                if ($request->filled('users')) {
                    foreach ($request->users as $userId) {
                        VoucherUser::create([
                            'voucher_id' => $voucher->id,
                            'user_id' => $userId,
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('vouchers.index')->with('success', 'Voucher berhasil ditambahkan.');
        } catch (\Throwable $th) {
            dd($th);
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $th->getMessage()]);
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

    public function status(Request $request, string $id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->update(['is_active' => $request->status]);
        return redirect()->route('vouchers.index')->with('success', 'Status updated successfully');
    }
}
