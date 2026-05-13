<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $galleries = Gallery::latest()->get();

        return view(
            'admin.gallery',
            compact('galleries')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE PAGE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.gallery-create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([

            'image' => 'required|image|mimes:jpg,jpeg,png,webp',

        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD IMAGE
        |--------------------------------------------------------------------------
        */

        $imageName = time() . '.' .
            $request->image->extension();

        $request->image->move(
            public_path('uploads/gallery'),
            $imageName
        );

        /*
        |--------------------------------------------------------------------------
        | SAVE DATABASE
        |--------------------------------------------------------------------------
        */

        Gallery::create([

            'title' => $request->title,

            'image' => $imageName,

            'description' => $request->description,

        ]);

        return redirect('/admin/gallery')
            ->with('success', 'Photo uploaded!');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT PAGE
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $gallery = Gallery::findOrFail($id);

        return view(
            'admin.gallery-edit',
            compact('gallery')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $gallery = Gallery::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | IMAGE UPDATE
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('image')) {

            $imageName = time() . '.' .
                $request->image->extension();

            $request->image->move(
                public_path('uploads/gallery'),
                $imageName
            );

            $gallery->image = $imageName;
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE DATA
        |--------------------------------------------------------------------------
        */

        $gallery->update([

            'title' => $request->title,

            'description' => $request->description,

        ]);

        $gallery->save();

        return redirect('/admin/gallery')
            ->with('success', 'Gallery updated!');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);

        $gallery->delete();

        return redirect('/admin/gallery')
            ->with('success', 'Gallery deleted!');
    }

    public function userIndex()
    {
        $galleries = Gallery::latest()->get();

        return view(
            'user.gallery',
            compact('galleries')
        );
    }
}