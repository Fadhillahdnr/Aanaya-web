<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Music;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MusicController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $musics = Music::latest()->get();

        return view('admin.music', compact('musics'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.music-create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'artist' => 'required',
            'cover_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'audio_file' => 'required|file|max:20480',
            'spotify_link' => 'nullable',
            'youtube_link' => 'nullable',
            'description' => 'nullable',
            'release_date' => 'nullable|date',
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD COVER
        |--------------------------------------------------------------------------
        */

        $coverName = time() . '_cover.' .
            $request->cover_image->extension();

        $request->cover_image->move(
            public_path('uploads/covers'),
            $coverName
        );

        /*
        |--------------------------------------------------------------------------
        | UPLOAD AUDIO
        |--------------------------------------------------------------------------
        */

        $audioName = time() . '_audio.' .
            $request->audio_file->extension();

        $request->audio_file->move(
            public_path('uploads/music'),
            $audioName
        );

        /*
        |--------------------------------------------------------------------------
        | SAVE DATABASE
        |--------------------------------------------------------------------------
        */

        Music::create([

            'title'         => $request->title,

            'artist'        => $request->artist,

            'slug'          => Str::slug($request->title),

            'cover_image'   => 'uploads/covers/' . $coverName,

            'audio_file'    => 'uploads/music/' . $audioName,

            'spotify_link'  => $request->spotify_link,

            'youtube_link'  => $request->youtube_link,

            'description'   => $request->description,

            'release_date'  => $request->release_date,

        ]);

        return redirect()
            ->route('admin.music')
            ->with('success', 'Music uploaded successfully ✨');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $music = Music::findOrFail($id);

        return view('admin.music-edit', compact('music'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $music = Music::findOrFail($id);

        $music->update([
            'title'         => $request->title,
            'artist'        => $request->artist,
            'spotify_link'  => $request->spotify_link,
            'youtube_link'  => $request->youtube_link,
            'description'   => $request->description,
            'release_date'  => $request->release_date,
        ]);

        return redirect()
            ->route('admin.music')
            ->with('success', 'Music updated ✨');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $music = Music::findOrFail($id);

        $music->delete();

        return redirect()
            ->route('admin.music')
            ->with('success', 'Music deleted');
    }

    public function userIndex()
    {
        $musics = Music::latest()->get();

        return view('user.music', compact('musics'));
    }
}