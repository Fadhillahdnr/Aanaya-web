<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Gallery;
use App\Models\Music;
use App\Models\MusicVideo;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class UserDashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | LATEST DATA
        |--------------------------------------------------------------------------
        */

        $data = Cache::remember('public.dashboard.v2', now()->addMinutes(10), function () {
            return [
                'latestMusic' => Music::latest()->first(),
                'latestArticle' => Article::latest()->first(),
                'latestProduct' => Product::latest()->first(),
                'latestGallery' => Gallery::latest()->first(),
                'latestVideos' => MusicVideo::latest()->take(5)->get(),
                'totalMusic' => Music::count(),
                'totalArticles' => Article::count(),
                'totalProducts' => Product::count(),
                'totalGallery' => Gallery::count(),
                'recentMusics' => Music::latest()->take(6)->get(),
                'recentArticles' => Article::latest()->take(3)->get(),
                'featuredProducts' => Product::latest()->take(4)->get(),
                'recentGallery' => Gallery::latest()->take(8)->get(),
            ];
        });

        return view('user.dashboard', $data);
    }

    public function Musicindex()
    {
        $data = Cache::remember('public.music.v3', now()->addMinutes(10), function () {
            $recentMusics = Music::query()
                ->latest('created_at')
                ->latest('id')
                ->take(12)
                ->get();

            return [
                'latestMusic' => $recentMusics->first(),
                'recentMusics' => $recentMusics,
                'totalMusic' => Music::count(),
            ];
        });

        return view('user.music', $data);
    }
}
