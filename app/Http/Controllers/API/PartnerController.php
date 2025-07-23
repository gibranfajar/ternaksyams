<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AffiliatorBenefit;
use App\Models\Partner;
use App\Models\PartnerAccount;
use App\Models\ResellerBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    public function reseller(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'whatsapp_number' => 'required',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'postal_code' => 'required',
            'bank_name' => 'required',
            'account_number' => 'required',
            'account_name' => 'required',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        try {
            $partner = Partner::create([
                'type' => 'reseller',
                'name' => $request->name,
                'email' => $request->email,
                'whatsapp_number' => $request->whatsapp_number,
                'address' => $request->address,
                'province' => $request->province,
                'city' => $request->city,
                'subdistrict' => $request->subdistrict,
                'postal_code' => $request->postal_code,
            ]);

            PartnerAccount::create([
                'partner_id' => $partner->id,
                'card' => $request->bank_name,
                'card_number' => $request->account_number,
                'card_name' => $request->account_name
            ]);

            return response()->json([
                'message' => 'Partner created successfully',
                'data' => $partner
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function affiliate(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'whatsapp_number' => 'required',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'postal_code' => 'required',
            'sosmed' => 'nullable',
            'shopee' => 'nullable',
            'tokopedia' => 'nullable',
            'tiktok' => 'nullable',
            'lazada' => 'nullable',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }

        try {
            $partner = Partner::create([
                'type' => 'affiliator',
                'name' => $request->name,
                'email' => $request->email,
                'whatsapp_number' => $request->whatsapp_number,
                'address' => $request->address,
                'province' => $request->province,
                'city' => $request->city,
                'subdistrict' => $request->subdistrict,
                'postal_code' => $request->postal_code,
            ]);

            PartnerAccount::create([
                'partner_id' => $partner->id,
                'sosmed_account' => $request->sosmed,
                'shopee' => $request->shopee,
                'tokopedia' => $request->tokopedia,
                'tiktok' => $request->tiktok,
                'lazada' => $request->lazada,
            ]);

            return response()->json([
                'message' => 'Partner created successfully',
                'data' => $partner
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function resellerBenefit()
    {
        $data = ResellerBenefit::all();
        return response()->json([
            'data' => $data
        ], 200);
    }

    public function affiliateBenefit()
    {
        $data = AffiliatorBenefit::all();
        return response()->json([
            'data' => $data
        ], 200);
    }
}
