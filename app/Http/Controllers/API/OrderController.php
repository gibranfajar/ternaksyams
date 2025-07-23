<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Shipping;
use App\Models\ShippingOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function createTransaction(Request $request)
    {
        $request->validate([
            'voucher_code' => 'nullable|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'no_whatsapp' => 'required|string|max:20',
            'address' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'subdistrict' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'courier' => 'required|string',
            'service' => 'required|string',
            'cost' => 'required|numeric',
            'etd' => 'required|string',
            'cart_id' => 'required|integer|exists:carts,id',
            'payment_method' => 'required|string', // ✅ tambahkan ini
            'note' => 'nullable|string'
        ]);

        $cart = Cart::with([
            'items.variantSize.variant.product',
            'items.variantSize.variant.flavour',
            'items.variantSize.variant.images',
            'items.variantSize.size',
            'items.variantSize.flashSales'
        ])->find($request->cart_id);

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['message' => 'Keranjang kosong'], 400);
        }

        // authenticated user
        $user = Auth::guard('sanctum')->user();

        // Generate invoice
        $invoice = 'INV/TS/' . now()->format('Ymd') . '/' . rand(1000, 9999);

        DB::beginTransaction();
        try {
            $total = $cart->items->sum(function ($item) {
                return $item->variantSize->price * $item->quantity;
            });

            $fixTotal = $total + $request->cost;

            // Step 1: Set API key Xendit
            \Xendit\Configuration::setXenditKey(env('XENDIT_SECRET_KEY'));

            // Step 2: Init API instance
            $xenditApi = new \Xendit\PaymentMethod\PaymentMethodApi();

            // Step 3: Hitung expire time 24 jam
            $expiresAt = (new \DateTime())->add(new \DateInterval('PT24H'))->format(\DateTime::ATOM); // ISO 8601 format

            // Step 4: Buat parameter untuk VA Xendit
            $referenceId = $invoice; // pakai invoice kita untuk ID unik

            $params = new \Xendit\PaymentMethod\PaymentMethodParameters([
                'type' => 'VIRTUAL_ACCOUNT',
                'reference_id' => $referenceId,
                'reusability' => 'ONE_TIME_USE',
                'virtual_account' => [
                    'channel_code' => $request->payment_method, // contoh: BRI, BNI
                    'amount' => $fixTotal,
                    'currency' => 'IDR',
                    'channel_properties' => [
                        'customer_name' => $request->name,
                        'expires_at' => $expiresAt
                    ]
                ]
            ]);

            // Step 5: Eksekusi ke Xendit
            $xenditResult = $xenditApi->createPaymentMethod(null, $params);

            // Create shipping option
            $shippingOption = ShippingOption::create([
                'courier' => $request->courier,
                'service' => $request->service,
                'cost' => $request->cost,
                'etd' => $request->etd
            ]);

            // Create shipping
            $shipping = Shipping::create([
                'shipping_option_id' => $shippingOption->id,
                'recipient_name' => $request->name,
                'email' => $request->email,
                'phone' => $request->no_whatsapp,
                'address' => $request->address,
                'province' => $request->province,
                'city' => $request->city,
                'district' => $request->district,
                'subdistrict' => $request->subdistrict,
                'postal_code' => $request->postal_code
            ]);

            // Create payment
            $payment = Payment::create([
                'method' => $request->payment_method,
                'invoice' => $invoice
            ]);

            // Create order
            $order = Order::create([
                'invoice' => $invoice,
                'user_id' => $user ? $user->id : null,
                'session_id' => session()->getId(),
                'cart_id' => $request->cart_id,
                'total' => $fixTotal,
                'shipping_id' => $shipping->id,
                'payment_id' => $payment->id,
                'note' => $request->note
            ]);

            // Create order items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_size_id' => $item->variantSize->id,
                    'price' => $item->variantSize->price,
                    'quantity' => $item->quantity,
                    'total' => $item->variantSize->price * $item->quantity
                ]);
            }

            DB::commit();

            // update status cart
            $cart->update([
                'status' => 'ordered'
            ]);

            return response()->json([
                'message' => 'Order created successfully',
                'data' => [
                    'order_id' => $order->id,
                    'invoice' => $invoice,
                    'payment_method' => $request->payment_method,
                    'xendit_va' => $xenditResult
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat membuat transaksi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
