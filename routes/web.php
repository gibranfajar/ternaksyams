<?php

use App\Http\Controllers\Affiliators\AffiliateController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Faqs\FaqController;
use App\Http\Controllers\Faqs\CategoryController as FaqCategoryController;
use App\Http\Controllers\Tutorials\TutorialController;
use App\Http\Controllers\Tutorials\CategoryController as TutorialCategoryController;
use App\Http\Controllers\Articles\ArticleController;
use App\Http\Controllers\Articles\CategoryController as ArticleCategoryController;
use App\Http\Controllers\Affiliators\BenefitController as AffiliatorBenefitController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Resellers\BenefitController as ResellerBenefitController;
use App\Http\Controllers\Products\FlashsaleController as FlashSaleProductController;
use App\Http\Controllers\Products\CategoryController as CategoryProductController;
use App\Http\Controllers\Products\FlavourController as FlavourProductController;
use App\Http\Controllers\Products\SizeController as SizeProductController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\Resellers\ResellerController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

Route::resource('/', AuthController::class);
Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

// PRODUCTS
Route::resource('/products/categories', CategoryProductController::class);
Route::resource('/products/flavours', FlavourProductController::class);
Route::resource('/products/sizes', SizeProductController::class);
Route::resource('/products', ProductController::class);
Route::put('/products/{product}/status', [ProductController::class, 'status'])->name('products.status');
Route::resource('/flashsales', FlashSaleProductController::class);

// PROMOTIONS
Route::resource('/promotions', PromotionController::class);
Route::put('/promotions/{promotion}/status', [PromotionController::class, 'status'])->name('promotions.changeStatus');

// RESELLERS
Route::resource('/resellers', ResellerController::class);
Route::resource('/reseller-benefits', ResellerBenefitController::class);
Route::put('/reseller-benefits/{resellerBenefit}/status', [ResellerBenefitController::class, 'status'])->name('reseller-benefits.changeStatus');

// AFFILIATORS
Route::resource('/affiliators', AffiliateController::class);
Route::resource('/affiliator-benefits', AffiliatorBenefitController::class);
Route::put('/affiliator-benefits/{affiliatorBenefit}/status', [AffiliatorBenefitController::class, 'status'])->name('affiliator-benefits.changeStatus');

// ARTICLES
Route::resource('/article-categories', ArticleCategoryController::class);
Route::resource('/articles', ArticleController::class);
Route::post('/articles/trix', [ArticleController::class, 'trixUpload'])->name('trix.upload');

// TUTORIALS
Route::resource('/tutorial-categories', TutorialCategoryController::class);
Route::resource('/tutorials', TutorialController::class);

// FAQS
Route::resource('/faq-categories', FaqCategoryController::class);
Route::resource('/faqs', FaqController::class);
Route::put('/faqs/{faq}/status', [FaqController::class, 'status'])->name('faqs.changeStatus');

// VOUCHERS
Route::resource('/vouchers', VoucherController::class);
Route::put('/vouchers/{voucher}/status', [VoucherController::class, 'status'])->name('vouchers.changeStatus');

Route::post('/addtocart', [CartController::class, 'addToCart']);

// ORDERS
Route::resource('/orders', OrderController::class);
