<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MusicController;
use App\Http\Controllers\Admin\MusicVideoController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\MediaUploadController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PUBLIC PAGES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

/*
|--------------------------------------------------------------------------
| PUBLIC USER PAGES
|--------------------------------------------------------------------------
| Bisa diakses guest tanpa login
*/

Route::get(
    '/dashboard',
    [UserDashboardController::class, 'index']
)->name('dashboard');

/*
|--------------------------------------------------------------------------
| MUSIC
|--------------------------------------------------------------------------
*/

Route::get(
    '/music',
    [UserDashboardController::class, 'Musicindex']
)->name('music');

/*
|--------------------------------------------------------------------------
| ARTICLES
|--------------------------------------------------------------------------
*/

Route::get(
    '/articles',
    [ArticleController::class, 'userIndex']
)->name('articles');

Route::get(
    '/articles/{id}',
    [ArticleController::class, 'show']
)->name('articles.show');

Route::get(
    '/comic/{slug}',
    [ArticleController::class, 'show']
)->name('articles.comic');

/*
|--------------------------------------------------------------------------
| GALLERY
|--------------------------------------------------------------------------
*/

Route::get(
    '/gallery',
    [GalleryController::class, 'userIndex']
)->name('gallery');

/*
|--------------------------------------------------------------------------
| MERCHANDISE
|--------------------------------------------------------------------------
*/

Route::get(
    '/merchandise',
    [ProductController::class, 'userIndex']
)->name('merchandise');

Route::get(
    '/merchandise/{slug}',
    [ProductController::class, 'show']
)->name('merchandise.show');

/*
|--------------------------------------------------------------------------
| ABOUT
|--------------------------------------------------------------------------
*/

Route::view(
    '/about',
    'user.about'
)->name('about');

/*
|--------------------------------------------------------------------------
| AUTH REQUIRED
|--------------------------------------------------------------------------
| Harus login
*/

Route::middleware(['auth'])->group(function () {

    Route::get('/orders', [UserOrderController::class, 'index'])
        ->name('orders.index');

    Route::get('/orders/{order}', [UserOrderController::class, 'show'])
        ->name('orders.show');

    Route::get('/orders/{order}/status', [UserOrderController::class, 'status'])
        ->name('orders.status');

    Route::post('/media/uploads/sign', [MediaUploadController::class, 'sign'])
        ->middleware('throttle:30,1')
        ->name('media.uploads.sign');

    Route::post('/media/uploads/{media}/complete', [MediaUploadController::class, 'complete'])
        ->middleware('throttle:60,1')
        ->name('media.uploads.complete');

    /*
    |--------------------------------------------------------------------------
    | CART
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/cart',
        [CartController::class, 'index']
    )->name('cart.index');

    Route::post(
        '/cart/add/{id}',
        [CartController::class, 'add']
    )->name('cart.add');

    Route::delete(
        '/cart/remove/{id}',
        [CartController::class, 'remove']
    )->name('cart.remove');

    Route::post(
        '/cart/update/{id}',
        [CartController::class, 'updateQuantity']
    )->name('cart.update');

    /*
    |--------------------------------------------------------------------------
    | CHECKOUT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/checkout',
        [CheckoutController::class, 'index']
    )->name('checkout');

    Route::post(
        '/checkout/process',
        [CheckoutController::class, 'process']
    )->name('checkout.process');

    /*
    |--------------------------------------------------------------------------
    | PROFILE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | MUSIC ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/music', [MusicController::class, 'index'])
        ->name('admin.music');

    Route::get('/admin/music/create', [MusicController::class, 'create'])
        ->name('admin.music.create');

    Route::post('/admin/music/store', [MusicController::class, 'store'])
        ->name('admin.music.store');

    Route::get('/admin/music/{id}/edit', [MusicController::class, 'edit'])
        ->name('admin.music.edit');

    Route::put('/admin/music/{id}', [MusicController::class, 'update'])
        ->name('admin.music.update');

    Route::delete('/admin/music/{id}', [MusicController::class, 'destroy'])
        ->name('admin.music.destroy');

    /*
    |--------------------------------------------------------------------------
    | ARTICLES ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/articles', [ArticleController::class, 'index']);

    Route::get('/admin/articles/create', [ArticleController::class, 'create']);

    Route::post('/admin/articles/store', [ArticleController::class, 'store']);

    Route::get('/admin/articles/{id}/edit', [ArticleController::class, 'edit']);

    Route::put('/admin/articles/{id}', [ArticleController::class, 'update']);

    Route::delete('/admin/articles/{id}', [ArticleController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/products', [ProductController::class, 'index']);

    Route::get('/admin/products/create', [ProductController::class, 'create']);

    Route::post('/admin/products/store', [ProductController::class, 'store']);

    Route::get('/admin/products/{id}/edit', [ProductController::class, 'edit']);

    Route::put('/admin/products/{id}', [ProductController::class, 'update']);

    Route::delete('/admin/products/{id}', [ProductController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | GALLERY ADMIN
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/gallery', [GalleryController::class, 'index']);

    Route::get('/admin/gallery/create', [GalleryController::class, 'create']);

    Route::post('/admin/gallery/store', [GalleryController::class, 'store']);

    Route::get('/admin/gallery/{id}/edit', [GalleryController::class, 'edit']);

    Route::put('/admin/gallery/{id}', [GalleryController::class, 'update']);

    Route::delete('/admin/gallery/{id}', [GalleryController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | USER MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/users',
        [UserController::class, 'index']
    );

    Route::get(
        '/admin/users/create',
        [UserController::class, 'create']
    );

    Route::post(
        '/admin/users/store',
        [UserController::class, 'store']
    );

    Route::get(
        '/admin/users/{user}',
        [UserController::class, 'show']
    )->name('admin.users.show');

    Route::get(
        '/admin/users/{user}/edit',
        [UserController::class, 'edit']
    );

    Route::put(
        '/admin/users/{user}/update',
        [UserController::class, 'update']
    );

    Route::delete(
        '/admin/users/{user}/delete',
        [UserController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | MUSIC VIDEO
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/mv',
        [MusicVideoController::class, 'index']
    )->name('admin.music-vidio');

    Route::get(
        '/admin/mv/create',
        [MusicVideoController::class, 'create']
    )->name('admin.music-vidio-create');

    Route::post(
        '/admin/mv/store',
        [MusicVideoController::class, 'store']
    )->name('admin.music-vidio.store');

    Route::get(
        '/admin/mv/{mv}/edit',
        [MusicVideoController::class, 'edit']
    )->name('admin.music-vidio.edit');

    Route::put(
        '/admin/mv/{mv}',
        [MusicVideoController::class, 'update']
    )->name('admin.music-vidio.update');

    Route::delete(
        '/admin/mv/{mv}/delete',
        [MusicVideoController::class, 'destroy']
    )->name('admin.music-vidio.destroy');

    /*
    |--------------------------------------------------------------------------
    | ORDERS ADMIN
    |--------------------------------------------------------------------------
    */
    Route::get(
        '/admin/orders',
        [OrderController::class, 'index']
    )->name('admin.orders');

    Route::get(
        '/admin/orders/{order}',
        [OrderController::class, 'show']
    )->name('admin.orders.show');

    Route::put(
        '/admin/orders/{order}/status',
        [OrderController::class, 'updateStatus']
    )->name('admin.orders.status');

});

require __DIR__.'/auth.php';
