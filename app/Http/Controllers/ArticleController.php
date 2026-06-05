<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ComicImage;
use App\Models\ArticleBlock;
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
    | EDIT PAGE
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $article = Article::with([
            'comicImages',
            'blocks'
        ])->findOrFail($id);

        return view(
            'admin.article-edit',
            compact('article')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            /*
            |------------------------------------------------------------------
            | COMMON
            |------------------------------------------------------------------
            */

            'category' => 'required|in:article,comic',

            'title' => 'required|max:255',

            'thumbnail' =>
                'required|image|mimes:jpg,jpeg,png,webp',

            'published_at' =>
                'nullable|date',

            /*
            |------------------------------------------------------------------
            | ARTICLE
            |------------------------------------------------------------------
            */
            'blocks' => 'required_if:category,article|array',
            'blocks.*.type' => 'required_with:blocks|in:text,image',
            'blocks.*.content' => 'nullable|string',
            'blocks.*.image' => 'nullable|image|mimes:jpg,jpeg,png,webp',

            /*
            |------------------------------------------------------------------
            | COMIC
            |------------------------------------------------------------------
            */

            'description' => 'nullable:category,comic|string',

            'comic_images' =>
                'required_if:category,comic|array',

            'comic_images.*' =>
                'image|mimes:jpg,jpeg,png,webp',
        ]);

        DB::beginTransaction();

        try {

            /*
            |------------------------------------------------------------------
            | UPLOAD THUMBNAIL
            |------------------------------------------------------------------
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
            |------------------------------------------------------------------
            | CREATE ARTICLE
            |------------------------------------------------------------------
            */

            $article = Article::create([

                'category' => $request->category,

                'title' => $request->title,

                'slug' => Str::slug(
                    $request->title . '-' . time()
                ),

                'thumbnail' => $thumbnail,

                'thumbnail_public_id' =>
                    $thumbnailPublicId,

                'author_id' => Auth::id(),

                'published_at' =>
                    $request->published_at,

                /*
                |--------------------------------------------------------------
                | LEGACY FIELD
                |--------------------------------------------------------------
                */

                'content' => '',

                'description' =>
                    $request->category === 'comic'
                    ? $request->description
                    : null,
            ]);

            /*
            |------------------------------------------------------------------
            | ARTICLE BLOCKS
            |------------------------------------------------------------------
            */

            if (
                $request->category === 'article'
                &&
                is_array($request->blocks)
            ) {

                foreach (
                    array_values($request->blocks)
                    as $index => $block
                ) {

                    /*
                    |----------------------------------------------------------
                    | TEXT BLOCK
                    |----------------------------------------------------------
                    */

                    if (
                        isset($block['type'])
                        &&
                        $block['type'] === 'text'
                    ) {

                        if (
                            !empty($block['content'])
                        ) {

                            ArticleBlock::create([

                                'article_id' =>
                                    $article->id,

                                'type' => 'text',

                                'content' =>
                                    $block['content'],

                                'sort_order' =>
                                    $index,
                            ]);
                        }
                    }

                    /*
                    |----------------------------------------------------------
                    | IMAGE BLOCK
                    |----------------------------------------------------------
                    */

                    if (
                        isset($block['type'])
                        &&
                        $block['type'] === 'image'
                    ) {

                        if (
                            isset($block['image'])
                            &&
                            $block['image']
                            instanceof \Illuminate\Http\UploadedFile
                        ) {

                            $imageUpload =
                                $this->uploadToCloudinary(
                                    $block['image'],
                                    'articles/blocks'
                                );

                            ArticleBlock::create([

                                'article_id' =>
                                    $article->id,

                                'type' => 'image',

                                'image' =>
                                    $imageUpload['url'],

                                'image_public_id' =>
                                    $imageUpload['public_id'],

                                'sort_order' =>
                                    $index,
                            ]);
                        }
                    }
                }
            }

            /*
            |------------------------------------------------------------------
            | COMIC IMAGES
            |------------------------------------------------------------------
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

                        'article_id' =>
                            $article->id,

                        'image' =>
                            $comicUpload['url'],

                        'public_id' =>
                            $comicUpload['public_id'],

                        'sort_order' =>
                            $index,
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
                ->withErrors(
                    $e->getMessage()
                )
                ->withInput();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $article = Article::with([
            'blocks',
            'comicImages'
        ])->findOrFail($id);

        $request->validate([

            'category' => 'required|in:article,comic',

            'title' => 'required|string|max:255',

            'thumbnail' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            /*
            |--------------------------------------------------------------------------
            | ARTICLE
            |--------------------------------------------------------------------------
            */

            'blocks' =>
                'nullable|array',

            'blocks.*.id' =>
                'nullable|integer',

            'blocks.*.type' =>
                'required_with:blocks|in:text,image',

            'blocks.*.content' =>
                'nullable|string',

            'blocks.*.image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            /*
            |--------------------------------------------------------------------------
            | COMIC
            |--------------------------------------------------------------------------
            */

            'description' =>
                'nullable|string',

            'comic_images.*' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',

            'published_at' =>
                'nullable|date',
        ]);

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | THUMBNAIL
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('thumbnail')) {

                if (!empty($article->thumbnail_public_id)) {

                    $this->deleteFromCloudinary(
                        $article->thumbnail_public_id
                    );
                }

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
            | BASIC DATA
            |--------------------------------------------------------------------------
            */

            $oldCategory =
                $article->category;

            $oldTitle =
                $article->title;

            $article->category =
                $request->category;

            $article->title =
                $request->title;

            if ($oldTitle !== $request->title) {

                $article->slug =
                    Str::slug($request->title)
                    . '-'
                    . time();
            }

            $article->description =
                $request->category === 'comic'
                    ? $request->description
                    : null;

            $article->published_at =
                $request->published_at;

            $article->save();

            /*
            |--------------------------------------------------------------------------
            | COMIC -> ARTICLE
            |--------------------------------------------------------------------------
            */

            if (
                $oldCategory === 'comic'
                &&
                $request->category === 'article'
            ) {

                foreach ($article->comicImages as $comicImage) {

                    if (!empty($comicImage->public_id)) {

                        $this->deleteFromCloudinary(
                            $comicImage->public_id
                        );
                    }

                    $comicImage->delete();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ARTICLE -> COMIC
            |--------------------------------------------------------------------------
            */

            if (
                $oldCategory === 'article'
                &&
                $request->category === 'comic'
            ) {

                foreach ($article->blocks as $block) {

                    if (
                        $block->type === 'image'
                        &&
                        !empty($block->image_public_id)
                    ) {

                        $this->deleteFromCloudinary(
                            $block->image_public_id
                        );
                    }

                    $block->delete();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ARTICLE BLOCKS
            |--------------------------------------------------------------------------
            */

            if ($request->category === 'article') {

                $submittedBlockIds = [];

                if (is_array($request->blocks)) {

                    foreach (
                        array_values($request->blocks)
                        as $sortOrder => $blockData
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | UPDATE EXISTING BLOCK
                        |--------------------------------------------------------------------------
                        */

                        if (!empty($blockData['id'])) {

                            $block =
                                ArticleBlock::where(
                                    'article_id',
                                    $article->id
                                )
                                ->find($blockData['id']);

                            if (!$block) {
                                continue;
                            }

                            $submittedBlockIds[] =
                                $block->id;

                            $block->sort_order =
                                $sortOrder;

                            /*
                            |--------------------------------------------------------------------------
                            | TEXT BLOCK
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $blockData['type']
                                === 'text'
                            ) {

                                $block->type =
                                    'text';

                                $block->content =
                                    $blockData['content'] ?? null;

                                $block->save();
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | IMAGE BLOCK
                            |--------------------------------------------------------------------------
                            */

                            if (
                                $blockData['type']
                                === 'image'
                            ) {

                                $block->type =
                                    'image';

                                if (
                                    isset($blockData['image'])
                                    &&
                                    $blockData['image']
                                    instanceof \Illuminate\Http\UploadedFile
                                ) {

                                    if (
                                        !empty(
                                            $block->image_public_id
                                        )
                                    ) {

                                        $this->deleteFromCloudinary(
                                            $block->image_public_id
                                        );
                                    }

                                    $upload =
                                        $this->uploadToCloudinary(
                                            $blockData['image'],
                                            'articles/blocks'
                                        );

                                    $block->image =
                                        $upload['url'];

                                    $block->image_public_id =
                                        $upload['public_id'];
                                }

                                $block->save();
                            }

                            continue;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | NEW TEXT BLOCK
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $blockData['type']
                            === 'text'
                        ) {

                            if (
                                empty(
                                    trim(
                                        $blockData['content']
                                        ?? ''
                                    )
                                )
                            ) {
                                continue;
                            }

                            $newBlock =
                                ArticleBlock::create([

                                    'article_id' =>
                                        $article->id,

                                    'type' =>
                                        'text',

                                    'content' =>
                                        $blockData['content'],

                                    'sort_order' =>
                                        $sortOrder,
                                ]);

                            $submittedBlockIds[] =
                                $newBlock->id;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | NEW IMAGE BLOCK
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $blockData['type']
                            === 'image'
                            &&
                            isset($blockData['image'])
                            &&
                            $blockData['image']
                            instanceof \Illuminate\Http\UploadedFile
                        ) {

                            $upload =
                                $this->uploadToCloudinary(
                                    $blockData['image'],
                                    'articles/blocks'
                                );

                            $newBlock =
                                ArticleBlock::create([

                                    'article_id' =>
                                        $article->id,

                                    'type' =>
                                        'image',

                                    'image' =>
                                        $upload['url'],

                                    'image_public_id' =>
                                        $upload['public_id'],

                                    'sort_order' =>
                                        $sortOrder,
                                ]);

                            $submittedBlockIds[] =
                                $newBlock->id;
                        }
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | DELETE REMOVED BLOCKS
                |--------------------------------------------------------------------------
                */

                $deletedBlocks =
                    ArticleBlock::where(
                        'article_id',
                        $article->id
                    )
                    ->whereNotIn(
                        'id',
                        $submittedBlockIds
                    )
                    ->get();

                foreach ($deletedBlocks as $block) {

                    if (
                        $block->type === 'image'
                        &&
                        !empty(
                            $block->image_public_id
                        )
                    ) {

                        $this->deleteFromCloudinary(
                            $block->image_public_id
                        );
                    }

                    $block->delete();
                }
            }

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

                $lastOrder =
                    ComicImage::where(
                        'article_id',
                        $article->id
                    )->max('sort_order') ?? -1;

                foreach (
                    $request->file('comic_images')
                    as $index => $image
                ) {

                    $upload =
                        $this->uploadToCloudinary(
                            $image,
                            'articles/comics'
                        );

                    ComicImage::create([

                        'article_id' =>
                            $article->id,

                        'image' =>
                            $upload['url'],

                        'public_id' =>
                            $upload['public_id'],

                        'sort_order' =>
                            $lastOrder + $index + 1,
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

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return back()
                ->withErrors(
                    'Failed to update content. Please try again.'
                )
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
        $article = Article::with([
            'comicImages',
            'blocks'
        ])->findOrFail($id);

        DB::beginTransaction();

        try {

            /*
            |----------------------------------------------------------------------
            | DELETE ARTICLE BLOCK IMAGES
            |----------------------------------------------------------------------
            */

            foreach ($article->blocks as $block) {

                if (
                    $block->type === 'image'
                    &&
                    !empty($block->image_public_id)
                ) {

                    $this->deleteFromCloudinary(
                        $block->image_public_id
                    );
                }

                $block->delete();
            }

            /*
            |----------------------------------------------------------------------
            | DELETE COMIC IMAGES
            |----------------------------------------------------------------------
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
            |----------------------------------------------------------------------
            | DELETE THUMBNAIL
            |----------------------------------------------------------------------
            */

            if (!empty($article->thumbnail_public_id)) {

                $this->deleteFromCloudinary(
                    $article->thumbnail_public_id
                );
            }

            /*
            |----------------------------------------------------------------------
            | DELETE ARTICLE
            |----------------------------------------------------------------------
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

            \Log::error(
                'Article delete failed: '
                . $e->getMessage()
            );

            return back()
                ->withErrors(
                    'Delete failed: '
                    . $e->getMessage()
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
            'blocks',
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