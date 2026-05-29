<?php

namespace App\Http\Controllers;

use App\Models\Music;
use App\Models\Article;
use App\Models\Product;
use App\Models\Gallery;
use App\Models\MusicVideo;

class UserDashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | LATEST DATA
        |--------------------------------------------------------------------------
        */

        $latestMusic = Music::latest()->first();

        $latestArticle = Article::latest()->first();

        $latestProduct = Product::latest()->first();

        $latestGallery = Gallery::latest()->first();

        $latestVideos = MusicVideo::latest()->get();

        /*
        |--------------------------------------------------------------------------
        | TOTAL DATA
        |--------------------------------------------------------------------------
        */

        $totalMusic = Music::count();

        $totalArticles = Article::count();

        $totalProducts = Product::count();

        $totalGallery = Gallery::count();

        /*
        |--------------------------------------------------------------------------
        | RECENT DATA
        |--------------------------------------------------------------------------
        */

        $recentMusics = Music::latest()
            ->take(6)
            ->get();

        $recentArticles = Article::latest()
            ->take(3)
            ->get();

        $featuredProducts = Product::latest()
            ->take(4)
            ->get();

        $recentGallery = Gallery::latest()
            ->take(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('user.dashboard', compact(

            'latestMusic',
            'latestArticle',
            'latestProduct',
            'latestGallery',
            'latestVideos',

            'totalMusic',
            'totalArticles',
            'totalProducts',
            'totalGallery',

            'recentMusics',
            'recentArticles',
            'featuredProducts',
            'recentGallery'

        ));

    }

    public function Musicindex()
    {
        /*
        |--------------------------------------------------------------------------
        | LATEST DATA
        |--------------------------------------------------------------------------
        */

        $latestMusic = Music::latest()->first();

        $latestArticle = Article::latest()->first();

        $latestProduct = Product::latest()->first();

        $latestGallery = Gallery::latest()->first();

        /*
        |--------------------------------------------------------------------------
        | TOTAL DATA
        |--------------------------------------------------------------------------
        */

        $totalMusic = Music::count();

        $totalArticles = Article::count();

        $totalProducts = Product::count();

        $totalGallery = Gallery::count();

        /*
        |--------------------------------------------------------------------------
        | RECENT DATA
        |--------------------------------------------------------------------------
        */

        $recentMusics = Music::latest()
            ->take(6)
            ->get();

        $recentArticles = Article::latest()
            ->take(3)
            ->get();

        $featuredProducts = Product::latest()
            ->take(4)
            ->get();

        $recentGallery = Gallery::latest()
            ->take(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('user.music', compact(

            'latestMusic',
            'latestArticle',
            'latestProduct',
            'latestGallery',

            'totalMusic',
            'totalArticles',
            'totalProducts',
            'totalGallery',

            'recentMusics',
            'recentArticles',
            'featuredProducts',
            'recentGallery'

        ));

    }
}