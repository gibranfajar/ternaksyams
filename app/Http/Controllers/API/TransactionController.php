<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $user = Auth::guard('sanctum')->user();

        $voucher = Voucher::with('products.productVariant.product')->where('code', $request->code)->first();

        if (!$voucher) {
            return response()->json(['message' => 'Voucher not found'], 404);
        }

        // Default kosong, isi hanya jika voucher type-nya 'product'
        $products = [];
        if ($voucher->type === 'product') {
            $products = $voucher->products
                ->map(function ($vp) {
                    return [
                        'id' => $vp->productVariant->id ?? null,
                        'name' => trim(($vp->productVariant->product->name ?? '') . ' - ' . ($vp->productVariant->flavour->name ?? '')),
                    ];
                })
                ->filter(fn($p) => $p['id'] !== null)
                ->values()
                ->toArray();
        }


        // 🔐 Cek apakah voucher khusus user login
        if ($voucher->target_audience === 'user' && !$user) {
            return response()->json(['message' => 'Login required to use this voucher'], 401);
        }

        // 📅 Validasi tanggal berlaku
        $today = now()->toDateString();

        if ($voucher->start_date > $today) {
            return response()->json(['message' => 'Voucher belum berlaku'], 200);
        }

        if ($voucher->end_date < $today) {
            return response()->json(['message' => 'Voucher sudah kadaluarsa'], 200);
        }

        // ✅ Status aktif
        if (!$voucher->is_active) {
            return response()->json(['message' => 'Voucher tidak aktif'], 200);
        }

        // ✅ Voucher valid
        return response()->json([
            'message' => 'Voucher berhasil diterapkan',
            'data' => [
                'code' => $voucher->code,
                'type' => $voucher->type,
                'discount_type' => $voucher->amount_type,
                'discount_value' => intval($voucher->amount),
                'min_purchase' => intval($voucher->min_transaction),
                'max_discount' => intval($voucher->max_discount),
                'target_audience' => $voucher->target_audience,
                'start_date' => $voucher->start_date,
                'end_date' => $voucher->end_date,
                'products' => $products
            ]
        ]);
    }
}
