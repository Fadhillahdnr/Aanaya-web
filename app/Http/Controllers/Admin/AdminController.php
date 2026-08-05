<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Gallery;
use App\Models\Music;
use App\Models\MusicVideo;
use App\Models\Product;

class AdminController extends Controller
{
    public function dashboard()
    {
        /*
        |--------------------------------------------------------------------------
        | TOTAL DATA
        |--------------------------------------------------------------------------
        */

        $totalMusic = Music::count();

        $totalArticles = class_exists(Article::class)
            ? Article::count()
            : 0;

        $totalProducts = class_exists(Product::class)
            ? Product::count()
            : 0;

        $totalGallery = class_exists(Gallery::class)
            ? Gallery::count()
            : 0;

        $latestVideo = MusicVideo::latest()->first();

        /*
        |--------------------------------------------------------------------------
        | LATEST RELEASE
        |--------------------------------------------------------------------------
        */

        $latestMusic = Music::latest()->first();

        return view('admin.dashboard', compact(
            'totalMusic',
            'totalArticles',
            'totalProducts',
            'totalGallery',
            'latestMusic',
            'latestVideo'
        ));
    }
}
