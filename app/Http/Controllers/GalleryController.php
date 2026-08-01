<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Services\MediaService;

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

    public function store(Request $request, MediaService $mediaService)
    {
        $request->validate([

            'uploaded_media.image' => 'required|string|exists:media,id',

        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD IMAGE
        |--------------------------------------------------------------------------
        */

        $media = $mediaService->fromRequest($request, 'image', true);

        /*
        |--------------------------------------------------------------------------
        | SAVE DATABASE
        |--------------------------------------------------------------------------
        */

        $gallery = Gallery::create([

            'title' => $request->title,

            'image' => $media->secure_url,

            'description' => $request->description,

        ]);

        $mediaService->claim($media, $gallery, 'image');

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

    public function update(Request $request, $id, MediaService $mediaService)
    {
        $gallery = Gallery::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | IMAGE UPDATE
        |--------------------------------------------------------------------------
        */

        if ($media = $mediaService->fromRequest($request, 'image')) {
            $oldMedia = $gallery->media()->where('purpose', 'image')->latest()->first();
            $gallery->image = $media->secure_url;
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

        if (isset($media)) {
            $mediaService->claim($media, $gallery, 'image');
            $mediaService->queueDelete($oldMedia?->public_id);
            $oldMedia?->delete();
        }

        return redirect('/admin/gallery')
            ->with('success', 'Gallery updated!');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id, MediaService $mediaService)
    {
        $gallery = Gallery::findOrFail($id);

        foreach ($gallery->media as $media) {
            $mediaService->queueDelete($media->public_id, $media->resource_type);
            $media->delete();
        }
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
