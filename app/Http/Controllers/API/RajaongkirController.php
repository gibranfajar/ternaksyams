<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaongkirController extends Controller
{
    public function getProvinces()
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_KEY')
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/province');

        return response()->json([
            'data' => $response['data']
        ]);
    }

    public function getCity($id)
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_KEY')
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/city/' . $id);

        return response()->json([
            'data' => $response['data']
        ]);
    }

    public function getDistrict($id)
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_KEY')
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/district/' . $id);

        return response()->json([
            'data' => $response['data']
        ]);
    }

    public function getSubdistrict($id)
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_KEY')
        ])->get('https://rajaongkir.komerce.id/api/v1/destination/sub-district/' . $id);

        return response()->json([
            'data' => $response['data']
        ]);
    }

    public function costCalculate(Request $request)
    {
        $response = Http::asForm() // penting: pakai form-urlencoded, bukan JSON
            ->withHeaders([
                'key' => env('RAJAONGKIR_KEY'),
            ])
            ->post('https://rajaongkir.komerce.id/api/v1/calculate/district/domestic-cost', [
                'origin' => $request->input('origin'),
                'destination' => $request->input('destination'),
                'weight' => $request->input('weight'),
                'courier' => $request->input('courier'),
                'price' => $request->input('price', 'lowest'),
            ]);

        // Kembalikan response ke frontend / view / api
        return response()->json([
            'data' => $response['data']
        ]);
    }
}
