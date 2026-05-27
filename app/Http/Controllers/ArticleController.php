<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ComicImage;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $articles = Article::latest()->get();

        return view(
            'admin.articles',
            compact('articles')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE PAGE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.article-create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([

            /*
            |--------------------------------------------------------------------------
            | COMMON
            |--------------------------------------------------------------------------
            */

            'category' => 'required|in:article,comic',

            'title' => 'required|max:255',

            /*
            |--------------------------------------------------------------------------
            | THUMBNAIL
            |--------------------------------------------------------------------------
            */

            'thumbnail' =>
                'required|image|mimes:jpg,jpeg,png,webp',

            /*
            |--------------------------------------------------------------------------
            | ARTICLE
            |--------------------------------------------------------------------------
            */

            'content' =>
                'required_if:category,article|nullable',

            'published_at' =>
                'nullable|date',

            /*
            |--------------------------------------------------------------------------
            | COMIC
            |--------------------------------------------------------------------------
            */

            'description' =>
                'required_if:category,comic|nullable',

            'comic_images' =>
                'required_if:category,comic|array',

            'comic_images.*' =>
                'image|mimes:jpg,jpeg,png,webp',
        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | UPLOAD THUMBNAIL
            |--------------------------------------------------------------------------
            */

            $thumbnail = null;

            if ($request->hasFile('thumbnail')) {

                $thumbnail =
                    time() . '_' .
                    $request->thumbnail->getClientOriginalName();

                $request->thumbnail->move(
                    public_path('uploads/articles'),
                    $thumbnail
                );
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE CONTENT
            |--------------------------------------------------------------------------
            */

            $article = Article::create([

                'category' => $request->category,

                'title' => $request->title,

                'slug' => Str::slug(
                    $request->title . '-' . time()
                ),

                /*
                |--------------------------------------------------------------------------
                | COMMON
                |--------------------------------------------------------------------------
                */

                'thumbnail' => $thumbnail,

                'author_id' => Auth::id(),

                'published_at' => $request->published_at,

                /*
                |--------------------------------------------------------------------------
                | ARTICLE
                |--------------------------------------------------------------------------
                */

                'content' =>
                    $request->category === 'article'
                    ? $request->content
                    : '',

                /*
                |--------------------------------------------------------------------------
                | COMIC
                |--------------------------------------------------------------------------
                */

                'description' =>
                    $request->category === 'comic'
                    ? $request->description
                    : null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | COMIC IMAGES
            |--------------------------------------------------------------------------
            */

            if (
                $request->category === 'comic'
                &&
                $request->hasFile('comic_images')
            ) {

                foreach (
                    $request->file('comic_images')
                    as $index => $image
                ) {

                    $imageName =
                        time() . '_' .
                        uniqid() . '_' .
                        $image->getClientOriginalName();

                    $image->move(
                        public_path('uploads/comics'),
                        $imageName
                    );

                    ComicImage::create([

                        'article_id' => $article->id,

                        'image' => $imageName,

                        'sort_order' => $index,
                    ]);
                }
            }

            DB::commit();

            return redirect('/admin/articles')
                ->with(
                    'success',
                    ucfirst($request->category)
                    . ' uploaded successfully ✨'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT PAGE
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $article = Article::with('comicImages')
            ->findOrFail($id);

        return view(
            'admin.article-edit',
            compact('article')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $article = Article::with('comicImages')
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $request->validate([

            'category' => 'required|in:article,comic',

            'title' => 'required|max:255',

            /*
            |--------------------------------------------------------------------------
            | THUMBNAIL
            |--------------------------------------------------------------------------
            */

            'thumbnail' =>
                'nullable|image|mimes:jpg,jpeg,png,webp',

            /*
            |--------------------------------------------------------------------------
            | ARTICLE
            |--------------------------------------------------------------------------
            */

            'content' =>
                'required_if:category,article|nullable',

            /*
            |--------------------------------------------------------------------------
            | COMIC
            |--------------------------------------------------------------------------
            */

            'description' =>
                'required_if:category,comic|nullable',

            'comic_images.*' =>
                'image|mimes:jpg,jpeg,png,webp',
        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | UPDATE THUMBNAIL
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('thumbnail')) {

                /*
                |--------------------------------------------------------------------------
                | DELETE OLD
                |--------------------------------------------------------------------------
                */

                if ($article->thumbnail) {

                    $oldThumbnail =
                        public_path(
                            'uploads/articles/' .
                            $article->thumbnail
                        );

                    if (file_exists($oldThumbnail)) {
                        unlink($oldThumbnail);
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | UPLOAD NEW
                |--------------------------------------------------------------------------
                */

                $thumbnail =
                    time() . '_' .
                    $request->thumbnail->getClientOriginalName();

                $request->thumbnail->move(
                    public_path('uploads/articles'),
                    $thumbnail
                );

                $article->thumbnail = $thumbnail;
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE MAIN DATA
            |--------------------------------------------------------------------------
            */

            $article->category = $request->category;

            $article->title = $request->title;

            $article->slug = Str::slug(
                $request->title . '-' . time()
            );

            /*
            |--------------------------------------------------------------------------
            | ARTICLE
            |--------------------------------------------------------------------------
            */

            $article->content =
                $request->category === 'article'
                ? $request->content
                : '';

            /*
            |--------------------------------------------------------------------------
            | COMIC
            |--------------------------------------------------------------------------
            */

            $article->description =
                $request->category === 'comic'
                ? $request->description
                : null;

            /*
            |--------------------------------------------------------------------------
            | COMMON
            |--------------------------------------------------------------------------
            */

            $article->published_at =
                $request->published_at;

            $article->save();

            /*
            |--------------------------------------------------------------------------
            | ADD NEW COMIC IMAGES
            |--------------------------------------------------------------------------
            */

            if (
                $request->category === 'comic'
                &&
                $request->hasFile('comic_images')
            ) {

                $lastOrder =
                    ComicImage::where(
                        'article_id',
                        $article->id
                    )->count();

                foreach (
                    $request->file('comic_images')
                    as $index => $image
                ) {

                    $imageName =
                        time() . '_' .
                        uniqid() . '_' .
                        $image->getClientOriginalName();

                    $image->move(
                        public_path('uploads/comics'),
                        $imageName
                    );

                    ComicImage::create([

                        'article_id' => $article->id,

                        'image' => $imageName,

                        'sort_order' =>
                            $lastOrder + $index,
                    ]);
                }
            }

            DB::commit();

            return redirect('/admin/articles')
                ->with(
                    'success',
                    ucfirst($request->category)
                    . ' updated successfully ✨'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $article = Article::with('comicImages')
            ->findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | DELETE COMIC IMAGES
        |--------------------------------------------------------------------------
        */

        foreach ($article->comicImages as $comicImage) {

            $imagePath =
                public_path(
                    'uploads/comics/' .
                    $comicImage->image
                );

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $comicImage->delete();
        }

        /*
        |--------------------------------------------------------------------------
        | DELETE THUMBNAIL
        |--------------------------------------------------------------------------
        */

        if ($article->thumbnail) {

            $thumbnailPath =
                public_path(
                    'uploads/articles/' .
                    $article->thumbnail
                );

            if (file_exists($thumbnailPath)) {
                unlink($thumbnailPath);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DELETE ARTICLE
        |--------------------------------------------------------------------------
        */

        $article->delete();

        return redirect('/admin/articles')
            ->with(
                'success',
                ucfirst($article->category)
                . ' deleted successfully ✨'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | USER INDEX
    |--------------------------------------------------------------------------
    */

    public function userIndex()
    {
        $articles = Article::latest()->get();

        return view(
            'user.articles',
            compact('articles')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($slug)
    {
        $article = Article::with([
            'comicImages',
            'author'
        ])
        ->where('slug', $slug)
        ->firstOrFail();

        /*
        |--------------------------------------------------------------------------
        | COMIC VIEW
        |--------------------------------------------------------------------------
        */

        if ($article->category === 'comic') {

            $comic = $article;

            return view(
                'user.comic-show',
                compact('comic')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ARTICLE VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'user.article-show',
            compact('article')
        );
    }
}