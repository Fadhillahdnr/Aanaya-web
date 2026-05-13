<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\MusicController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;


/*
|--------------------------------------------------------------------------
| PUBLIC PAGES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| USER DASHBOARD
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get(
        '/dashboard',
        [UserDashboardController::class, 'index']
    )->name('dashboard');

    // MUSIC PAGE
    Route::get(
        '/music',
        [MusicController::class, 'userIndex']
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
    | CART & CHECKOUT
    |--------------------------------------------------------------------------
    */

    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/update/{id}', [CartController::class, 'updateQuantity'])->name('cart.update');

    Route::middleware('auth')->group(function () {

        Route::get('/checkout', [CheckoutController::class, 'index']);

        Route::post('/checkout/process', [CheckoutController::class, 'process']);
    });

    /*
    |--------------------------------------------------------------------------
    | ABOUT
    |--------------------------------------------------------------------------
    */

    Route::view(
        '/about',
        'user.about'
    )->name('about');

});

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->group(function () {

    Route::get('/admin', function () {
        return view('admin.dashboard');
    });

    Route::get('/admin/music', function () {
        return view('admin.music');
    });

    Route::get('/admin/articles', function () {
        return view('admin.articles');
    });

    Route::get('/admin/products', function () {
        return view('admin.products');
    });

    Route::get('/admin/gallery', function () {
        return view('admin.gallery');
    });

    Route::get('/admin', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    /*
    | MUSIC
    */

     // LIST MUSIC
    Route::get('/admin/music', [MusicController::class, 'index'])
        ->name('admin.music');

    // CREATE PAGE
    Route::get('/admin/music/create', [MusicController::class, 'create'])
        ->name('admin.music.create');

    // STORE MUSIC
    Route::post('/admin/music/store', [MusicController::class, 'store'])
        ->name('admin.music.store');

    // EDIT PAGE
    Route::get('/admin/music/{id}/edit', [MusicController::class, 'edit'])
        ->name('admin.music.edit');

    // UPDATE MUSIC
    Route::put('/admin/music/{id}', [MusicController::class, 'update'])
        ->name('admin.music.update');

    // DELETE MUSIC
    Route::delete('/admin/music/{id}', [MusicController::class, 'destroy'])
        ->name('admin.music.destroy');

    /*

    |--------------------------------------------------------------------------
    | ARTICLES
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
    | PRODUCTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/products',
        [ProductController::class, 'index']
    );

    Route::get(
        '/admin/products/create',
        [ProductController::class, 'create']
    );

    Route::post(
        '/admin/products/store',
        [ProductController::class, 'store']
    );

    Route::get(
        '/admin/products/{id}/edit',
        [ProductController::class, 'edit']
    );

    Route::put(
        '/admin/products/{id}',
        [ProductController::class, 'update']
    );

    Route::delete(
        '/admin/products/{id}',
        [ProductController::class, 'destroy']
    );

    /*
    |--------------------------------------------------------------------------
    | GALLERY
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admin/gallery',
        [GalleryController::class, 'index']
    );

    Route::get(
        '/admin/gallery/create',
        [GalleryController::class, 'create']
    );

    Route::post(
        '/admin/gallery/store',
        [GalleryController::class, 'store']
    );

    Route::get(
        '/admin/gallery/{id}/edit',
        [GalleryController::class, 'edit']
    );

    Route::put(
        '/admin/gallery/{id}',
        [GalleryController::class, 'update']
    );

    Route::delete(
        '/admin/gallery/{id}',
        [GalleryController::class, 'destroy']
    );

});


require __DIR__.'/auth.php';