<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ComicImage;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Cloudinary\Cloudinary;

class ArticleController extends Controller
{

    private function uploadToCloudinary(
        $file,
        $folder
    )
    {
        $cloudinary = app(Cloudinary::class);

        $upload = $cloudinary
            ->uploadApi()
            ->upload(
                $file->getRealPath(),
                [
                    'folder' => $folder
                ]
            );

        return [

            'url' => $upload['secure_url'],

            'public_id' => $upload['public_id'],

        ];
    }

    private function deleteFromCloudinary($publicId)
    {
        if (empty($publicId)) {
            return;
        }

        try {

            $cloudinary = app(Cloudinary::class);

            $cloudinary
                ->uploadApi()
                ->destroy($publicId);

        } catch (\Exception $e) {

            \Log::warning(
                'Cloudinary delete failed: '
                . $e->getMessage()
            );
        }
    }

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
            $thumbnailPublicId = null;

            if ($request->hasFile('thumbnail')) {

                $thumbnailUpload =
                    $this->uploadToCloudinary(
                        $request->file('thumbnail'),
                        'articles/thumbnails'
                    );

                $thumbnail =
                    $thumbnailUpload['url'];

                $thumbnailPublicId =
                    $thumbnailUpload['public_id'];
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
                'thumbnail_public_id' => $thumbnailPublicId,

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

                    $comicUpload =
                        $this->uploadToCloudinary(
                            $image,
                            'articles/comics'
                        );

                    ComicImage::create([

                        'article_id' => $article->id,

                        'image' => $comicUpload['url'],

                        'public_id' =>
                            $comicUpload['public_id'],

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
                | DELETE OLD THUMBNAIL
                |--------------------------------------------------------------------------
                */

                if (!empty($article->thumbnail_public_id)) {

                    try {

                        $this->deleteFromCloudinary(
                            $article->thumbnail_public_id
                        );

                    } catch (\Exception $e) {

                        \Log::warning(
                            'Cloudinary thumbnail delete failed: '
                            . $e->getMessage()
                        );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | UPLOAD NEW THUMBNAIL
                |--------------------------------------------------------------------------
                */

                $thumbnailUpload =
                    $this->uploadToCloudinary(
                        $request->file('thumbnail'),
                        'articles/thumbnails'
                    );

                $article->thumbnail =
                    $thumbnailUpload['url'];

                $article->thumbnail_public_id =
                    $thumbnailUpload['public_id'];
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

            $article->content =
                $request->category === 'article'
                ? $request->content
                : '';

            $article->description =
                $request->category === 'comic'
                ? $request->description
                : null;

            $article->published_at =
                $request->published_at;

            $article->save();

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

                    $comicUpload =
                        $this->uploadToCloudinary(
                            $image,
                            'articles/comics'
                        );

                    ComicImage::create([

                        'article_id' => $article->id,

                        'image' => $comicUpload['url'],

                        'public_id' =>
                            $comicUpload['public_id'],

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

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | DELETE COMIC IMAGES FROM CLOUDINARY
            |--------------------------------------------------------------------------
            */

            foreach ($article->comicImages as $comicImage) {

                if (!empty($comicImage->public_id)) {

                    $this->deleteFromCloudinary(
                        $comicImage->public_id
                    );
                }

                $comicImage->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE THUMBNAIL FROM CLOUDINARY
            |--------------------------------------------------------------------------
            */

            if (!empty($article->thumbnail_public_id)) {

                $this->deleteFromCloudinary(
                    $article->thumbnail_public_id
                );
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE ARTICLE
            |--------------------------------------------------------------------------
            */

            $article->delete();

            DB::commit();

            return redirect('/admin/articles')
                ->with(
                    'success',
                    ucfirst($article->category)
                    . ' deleted successfully ✨'
                );

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withErrors(
                    'Delete failed: ' . $e->getMessage()
                );
        }
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