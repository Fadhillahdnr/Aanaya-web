<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $articles = Article::latest()->get();

        return view('admin.articles', compact('articles'));
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
        $request->validate([
            'title'       => 'required|max:255',
            'thumbnail'   => 'required|image|mimes:jpg,jpeg,png,webp',
            'content'     => 'required',
            'published_at'=> 'nullable|date',
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD THUMBNAIL
        |--------------------------------------------------------------------------
        */

        $thumbnail = null;

        if ($request->hasFile('thumbnail')) {

            $thumbnail = time() . '_' .
                $request->thumbnail->getClientOriginalName();

            $request->thumbnail->move(
                public_path('uploads/articles'),
                $thumbnail
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE DATABASE
        |--------------------------------------------------------------------------
        */

        Article::create([
            'title'        => $request->title,
            'slug'         => Str::slug($request->title . '-' . time()),
            'thumbnail'    => $thumbnail,
            'content'      => $request->content,
            'author_id'    => Auth::id(),
            'published_at' => $request->published_at,
        ]);

        return redirect('/admin/articles')
            ->with('success', 'Article uploaded successfully ✨');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT PAGE
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $article = Article::findOrFail($id);

        return view('admin.article-edit', compact('article'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $request->validate([
            'title'       => 'required|max:255',
            'thumbnail'   => 'nullable|image|mimes:jpg,jpeg,png,webp',
            'content'     => 'required',
            'published_at'=> 'nullable|date',
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD NEW THUMBNAIL
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('thumbnail')) {

            $thumbnail = time() . '_' .
                $request->thumbnail->getClientOriginalName();

            $request->thumbnail->move(
                public_path('uploads/articles'),
                $thumbnail
            );

            $article->thumbnail = $thumbnail;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE DATA
        |--------------------------------------------------------------------------
        */

        $article->title = $request->title;
        $article->content = $request->content;
        $article->published_at = $request->published_at;

        $article->save();

        return redirect('/admin/articles')
            ->with('success', 'Article updated successfully ✨');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $article = Article::findOrFail($id);

        $article->delete();

        return redirect('/admin/articles')
            ->with('success', 'Article deleted');
    }

    public function userIndex()
    {
        $articles = Article::latest()->get();

        return view(
            'user.articles',
            compact('articles')
        );
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)->firstOrFail();

        return view('user.article-show', compact('article'));
    }
}