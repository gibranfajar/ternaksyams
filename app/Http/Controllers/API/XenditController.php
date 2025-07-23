<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\PaymentMethod\PaymentMethodApi;
use Xendit\PaymentMethod\PaymentMethodParameters;

class XenditController extends Controller
{
    public function callback(Request $request)
    {
        // Log semua payload webhook dari Xendit
        Log::channel('xendit')->info('Xendit Webhook:', $request->all());

        $data = $request->all();

        // Akses data yang benar dari struktur JSON
        $payload = $data['data'] ?? [];

        if (isset($payload['reference_id']) && isset($payload['status'])) {
            $order = Payment::where('invoice', $payload['reference_id'])->first();

            if ($order) {
                if ($payload['status'] === 'SUCCEEDED') {
                    $order->status = 'paid';
                    $order->paid_at = now(); // kalau ada kolom waktu bayar
                    $order->save();
                }
            }
        }

        return response()->json([
            'message' => 'Success'
        ], 200);
    }
}
