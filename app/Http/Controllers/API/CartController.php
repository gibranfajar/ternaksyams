<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductFlashSale;
use App\Models\ProductVariantSize;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_variant_sizes,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productVariantSizeId = $request->product_id;
        $quantity = $request->quantity;

        // ✅ Cek apakah user login via Sanctum token atau tidak
        $user = Auth::guard('sanctum')->user(); // ⬅️ token-based
        $sessionId = session()->getId();       // ⬅️ guest-based

        // ✅ Ambil data produk
        $variant = ProductVariantSize::find($productVariantSizeId);
        if (!$variant) {
            return response()->json(['error' => 'Produk tidak ditemukan'], 404);
        }

        // ✅ Cek apakah produk ada di flash sale aktif
        $now = Carbon::now();
        $flashSaleItem = ProductFlashSale::where('product_variant_size_id', $productVariantSizeId)
            ->whereHas('flashSale', function ($q) use ($now) {
                $q->where('start_date', '<=', $now)
                    ->where('end_date', '>=', $now);
            })->first();

        $availableFlashQty = $flashSaleItem?->quantity ?? 0;
        $availableNormalQty = $variant->quantity;
        $totalAvailable = $availableFlashQty + $availableNormalQty;

        if ($totalAvailable < $quantity) {
            return response()->json(['error' => 'Stok tidak mencukupi'], 400);
        }

        // ✅ Ambil cart (berdasarkan user atau session)
        $cart = Cart::firstOrCreate([
            'user_id'    => $user?->id,
            'session_id' => $user ? null : $sessionId,
            'status'     => 'active',
        ]);

        // ✅ Cari item di cart
        $cartItem = $cart->items()
            ->where('product_variant_size_id', $productVariantSizeId)
            ->first();

        if ($cartItem) {
            $newQty = $cartItem->quantity + $quantity;

            if ($totalAvailable < $newQty) {
                return response()->json(['error' => 'Jumlah melebihi stok tersedia'], 400);
            }

            $cartItem->quantity = $newQty;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_variant_size_id' => $productVariantSizeId,
                'quantity' => $quantity,
                'unit_price' => $flashSaleItem ? $flashSaleItem->price : $variant->price,
            ]);
        }

        // (Opsional) kurangi stok flash/normal saat checkout saja
        return response()->json(['message' => 'Produk berhasil ditambahkan ke keranjang']);
    }

    public function getCart(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $sessionId = session()->getId();

        $cart = Cart::with([
            'items.variantSize.variant.product',
            'items.variantSize.variant.flavour',
            'items.variantSize.variant.images',
            'items.variantSize.size',
            'items.variantSize.flashSales', // kalau kamu ada relasi ini
        ])
            ->where('status', 'active')
            ->when($user, fn($q) => $q->where('user_id', $user->id))
            ->when(!$user, fn($q) => $q->where('session_id', $sessionId))
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong'], 200);
        }

        return response()->json([
            'data' => $cart->items->map(function ($item) {
                $variantSize = $item->variantSize;
                $variant = $variantSize->variant;
                $product = $variant->product ?? null;
                $flavour = $variant->flavour ?? null;
                $image = $variant->images->first()->image ?? null;

                return [
                    'id' => $item->product_variant_size_id,
                    'quantity' => $item->quantity,
                    'original_price' => intval($variantSize->price),
                    'discount' => $variantSize->flashSales?->discount ?? $variantSize->discount,
                    'price' => $variantSize->flashSales ? intval($variantSize->flashSales->price) : intval($variantSize->price),
                    'is_flash_sale' => $variantSize->flashSales ? true : false,
                    'product' => [
                        'name' => trim(($product->name ?? '') . ' - ' . ($flavour->name ?? '')),
                        'size' => $variantSize->size->label ?? '',
                        'image' => $image,
                    ],
                ];
            }),
        ]);
    }

    public function increaseQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_variant_sizes,id',
        ]);

        $user = Auth::guard('sanctum')->user();
        $sessionId = session()->getId();
        $productVariantSizeId = $request->product_id;

        // Ambil cart
        $cart = Cart::where('status', 'active')
            ->when($user, fn($q) => $q->where('user_id', $user->id))
            ->when(!$user, fn($q) => $q->where('session_id', $sessionId))
            ->first();

        if (!$cart) return response()->json(['error' => 'Cart tidak ditemukan'], 404);

        // Cari item
        $cartItem = $cart->items()
            ->where('product_variant_size_id', $productVariantSizeId)
            ->first();

        if (!$cartItem) return response()->json(['error' => 'Item tidak ditemukan di cart'], 404);

        $variant = $cartItem->variantSize;
        $flashSale = $variant->flashSales;

        $available = ($flashSale?->quantity ?? 0) + $variant->quantity;

        if ($cartItem->quantity + 1 > $available) {
            return response()->json(['error' => 'Stok tidak mencukupi'], 400);
        }

        $cartItem->increment('quantity');

        return response()->json(['message' => 'Jumlah berhasil ditambahkan']);
    }

    public function decreaseQuantity(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_variant_sizes,id',
        ]);

        $user = Auth::guard('sanctum')->user();
        $sessionId = session()->getId();
        $productVariantSizeId = $request->product_id;

        // Ambil cart
        $cart = Cart::where('status', 'active')
            ->when($user, fn($q) => $q->where('user_id', $user->id))
            ->when(!$user, fn($q) => $q->where('session_id', $sessionId))
            ->first();

        if (!$cart) return response()->json(['error' => 'Cart tidak ditemukan'], 404);

        // Cari item
        $cartItem = $cart->items()
            ->where('product_variant_size_id', $productVariantSizeId)
            ->first();

        if (!$cartItem) return response()->json(['error' => 'Item tidak ditemukan di cart'], 404);

        if ($cartItem->quantity <= 1) {
            $cartItem->delete();
            return response()->json(['message' => 'Item dihapus dari keranjang']);
        }

        $cartItem->decrement('quantity');

        return response()->json(['message' => 'Jumlah berhasil dikurangi']);
    }


    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:product_variant_sizes,id',
        ]);

        $productVariantSizeId = $request->product_id;

        // 🔐 Cek user login/token atau guest
        $user = Auth::guard('sanctum')->user();
        $sessionId = session()->getId();

        // 🔍 Ambil cart user atau guest
        $cart = Cart::where('status', 'active')
            ->when($user, function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->when(!$user, function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
            })
            ->first();

        if (!$cart) {
            return response()->json(['error' => 'Keranjang tidak ditemukan'], 404);
        }

        // 🗑 Cari item dan hapus
        $deleted = $cart->items()
            ->where('product_variant_size_id', $productVariantSizeId)
            ->delete();

        if (!$deleted) {
            return response()->json(['error' => 'Produk tidak ada dalam keranjang'], 404);
        }

        return response()->json(['message' => 'Produk berhasil dihapus dari keranjang']);
    }
}
