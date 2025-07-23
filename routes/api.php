<?php

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController as CategoryProductController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PartnerController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\RajaongkirController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\XenditController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/user', [ApiAuthController::class, 'user'])->middleware('auth:sanctum');

// GOOGLE AUTH
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);

// API AUTH
Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');

// PARTNER
Route::post('/partner/reseller', [PartnerController::class, 'reseller']);
Route::post('/partner/affiliate', [PartnerController::class, 'affiliate']);
Route::get('/partner/reseller/benefit', [PartnerController::class, 'resellerBenefit']);
Route::get('/partner/affiliate/benefit', [PartnerController::class, 'affiliateBenefit']);

// CATEGORIES
Route::get('/categories', [CategoryProductController::class, 'getCategories']);

// PRODUCTS
Route::get('/product/variants', [ProductController::class, 'getVariantsAll']);
Route::get('/products', [ProductController::class, 'getProducts']);
Route::get('/products/{slug}', [ProductController::class, 'getProductDetail']);
// Flash Sale
Route::get('/flashsales', [ProductController::class, 'getProductFlashSale']);


// CART
Route::prefix('cart')->group(function () {
    Route::post('/add', [CartController::class, 'addToCart']);
    Route::get('/get', [CartController::class, 'getCart']);
    Route::post('/increase', [CartController::class, 'increaseQuantity']);
    Route::post('/decrease', [CartController::class, 'decreaseQuantity']);
    Route::post('/remove', [CartController::class, 'removeFromCart']);
});

// SHIPPING
// Route::get('/provinces', [ApiController::class, 'getProvinces']);
// Route::get('/cities/{id}', [ApiController::class, 'getCity']);
// Route::get('/districts/{id}', [ApiController::class, 'getDistrict']);
// Route::get('/subdistricts/{id}', [ApiController::class, 'getSubdistrict']);

// TRANSACTIONS
Route::post('/apply-voucher', [TransactionController::class, 'applyVoucher']);
Route::post('/checkout', [TransactionController::class, 'checkout']);
Route::get('/transactions', [TransactionController::class, 'transaction']);
Route::get('/transactions/{id}', [TransactionController::class, 'transactionDetail']);


// Promotion
Route::get('/promotions', [ApiController::class, 'promotion']);

// Voucher
Route::get('/vouchers', [ApiController::class, 'voucher']);
Route::get('/voucher/users', [ApiController::class, 'getVoucherUsers']);

// Arsip Tutorial
Route::get('/arsip-tutorial', [ApiController::class, 'arsipTutorial']);

// Articles
Route::get('/articles', [ApiController::class, 'article']);
Route::get('/articles/{slug}', [ApiController::class, 'articleDetail']);

// FAQ
Route::get('/faqs', [ApiController::class, 'faq']);


// RAJAONGKIR
Route::get('/provinces', [RajaongkirController::class, 'getProvinces']);
Route::get('/cities/{id}', [RajaongkirController::class, 'getCity']);
Route::get('/districts/{id}', [RajaongkirController::class, 'getDistrict']);
Route::get('/subdistricts/{id}', [RajaongkirController::class, 'getSubdistrict']);
// cost calculate
Route::post('/cost-calculate', [RajaongkirController::class, 'costCalculate']);


// TRANSACTION
Route::post('/transaction', [OrderController::class, 'createTransaction']);

// XENDIT
Route::post('/xendit/callback', [XenditController::class, 'callback']);
