<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Faq;
use App\Models\Promotion;
use App\Models\Tutorial;
use App\Models\Voucher;
use App\Models\VoucherUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function promotion()
    {
        $promotions = Promotion::all();
        return response()->json([
            'data' => $promotions
        ], 200);
    }

    public function voucher()
    {
        $voucher = Voucher::with('users', 'content', 'products.productVariant.product', 'products.productVariant.flavour')->get();
        return response()->json([
            'data' => $voucher
        ], 200);
    }

    // arsip tutorial
    public function arsipTutorial()
    {
        $tutorial = Tutorial::with('categories')->get();
        return response()->json([
            'data' => $tutorial
        ], 200);
    }

    // faqs
    public function faq()
    {
        $faqs = Faq::with('category', 'role')->latest()->get();
        return response()->json([
            'data' => $faqs
        ], 200);
    }

    // articles
    public function article()
    {
        $articles = Article::with('categories')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'data' => $articles
        ], 200);
    }

    // article detail
    public function articleDetail($slug)
    {
        $article = Article::with('categories')->where('slug', $slug)->first();
        return response()->json([
            'data' => $article
        ], 200);
    }

    // getProvinces
    public function getProvinces()
    {
        $response = Http::get('https://wilayah.id/api/provinces.json');

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Gagal mengambil data'], $response->status());
    }

    // getCity
    public function getCity($id)
    {
        $response = Http::get('https://wilayah.id/api/regencies/' . $id . '.json');

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Gagal mengambil data'], $response->status());
    }

    // getDistrict
    public function getDistrict($id)
    {
        $response = Http::get('https://wilayah.id/api/districts/' . $id . '.json');

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Gagal mengambil data'], $response->status());
    }

    // getSubdistrict
    public function getSubdistrict($id)
    {
        $response = Http::get('https://wilayah.id/api/villages/' . $id . '.json');

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Gagal mengambil data'], $response->status());
    }

    // getVoucherUsers
    public function getVoucherUsers()
    {
        $user = Auth::guard('sanctum')->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $voucherUsers = VoucherUser::with('voucher.products.productVariant', 'voucher.content')->where('user_id', $user->id)->get();

        $data = [];

        foreach ($voucherUsers as $item) {
            $voucher = $item->voucher;

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

            $content = [];
            if ($voucher->content) {
                $content = [
                    'id' => $voucher->content->id,
                    'name' => $voucher->content->title,
                    'description' => $voucher->content->description,
                    'banner' => $voucher->content->banner,
                    'used_at' => $voucher->content->used_at,
                    'tnc' => $voucher->content->tnc
                ];
            }


            $data[] = [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'type' => $voucher->type,
                'discount_type' => $voucher->amount_type,
                'discount_value' => intval($voucher->amount),
                'min_purchase' => intval($voucher->min_transaction),
                'max_discount' => intval($voucher->max_discount),
                'target_audience' => $voucher->target_audience,
                'start_date' => $voucher->start_date,
                'end_date' => $voucher->end_date,
                'products' => $products,
                'content' => $content
            ];
        }

        return response()->json([
            'user' => $user->name,
            'data' => $data
        ], 200);
    }
}
