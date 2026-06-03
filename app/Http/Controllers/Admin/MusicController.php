<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Music;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Cloudinary\Cloudinary;

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
            'title'         => 'required',
            'artist'        => 'required',
            'cover_image'   => 'required|image|mimes:jpg,jpeg,png,webp',
            'audio_file'    => 'required|file|max:51200',
            'spotify_link'  => 'nullable',
            'youtube_link'  => 'nullable',
            'description'   => 'nullable',
            'release_date'  => 'nullable|date',
        ]);

        $cloudinary = app(Cloudinary::class);

        /*
        |--------------------------------------------------------------------------
        | Upload Cover
        |--------------------------------------------------------------------------
        */

        $coverUpload = $cloudinary
            ->uploadApi()
            ->upload(
                $request->file('cover_image')->getRealPath(),
                [
                    'folder' => 'music/covers'
                ]
            );

        /*
        |--------------------------------------------------------------------------
        | Upload Audio
        |--------------------------------------------------------------------------
        */

        $audioUpload = $cloudinary
            ->uploadApi()
            ->upload(
                $request->file('audio_file')->getRealPath(),
                [
                    'folder' => 'music/audio',
                    'resource_type' => 'video'
                ]
            );

        Music::create([
            'title' => $request->title,

            'artist' => $request->artist,

            'slug' => Str::slug($request->title),

            'cover_image' =>
                $coverUpload['secure_url'],

            'cover_public_id' =>
                $coverUpload['public_id'],

            'audio_file' =>
                $audioUpload['secure_url'],

            'audio_public_id' =>
                $audioUpload['public_id'],

            'spotify_link' =>
                $request->spotify_link,

            'youtube_link' =>
                $request->youtube_link,

            'description' =>
                $request->description,

            'release_date' =>
                $request->release_date,
        ]);

        return redirect()
            ->route('admin.music')
            ->with(
                'success',
                'Music uploaded successfully ✨'
            );
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

        $request->validate([
            'title' => 'required',
            'artist' => 'required',
            'cover_image' => 'nullable|image',
            'audio_file' => 'required|file|max:51200',
        ]);

        $cloudinary = app(Cloudinary::class);

        $data = [

            'title' => $request->title,

            'artist' => $request->artist,

            'slug' => Str::slug($request->title),

            'spotify_link' =>
                $request->spotify_link,

            'youtube_link' =>
                $request->youtube_link,

            'description' =>
                $request->description,

            'release_date' =>
                $request->release_date,
        ];

        /*
        |--------------------------------------------------------------------------
        | Update Cover
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('cover_image')) {

            if (!empty($music->cover_public_id)) {

                try {

                    $cloudinary
                        ->uploadApi()
                        ->destroy(
                            $music->cover_public_id
                        );

                } catch (\Exception $e) {
                }
            }

            $coverUpload = $cloudinary
                ->uploadApi()
                ->upload(
                    $request->file('cover_image')
                        ->getRealPath(),
                    [
                        'folder' => 'music/covers'
                    ]
                );

            $data['cover_image'] =
                $coverUpload['secure_url'];

            $data['cover_public_id'] =
                $coverUpload['public_id'];
        }

        /*
        |--------------------------------------------------------------------------
        | Update Audio
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('audio_file')) {

            if (!empty($music->audio_public_id)) {

                try {

                    $cloudinary
                        ->uploadApi()
                        ->destroy(
                            $music->audio_public_id,
                            [
                                'resource_type' => 'video'
                            ]
                        );

                } catch (\Exception $e) {
                }
            }

            $audioUpload = $cloudinary
                ->uploadApi()
                ->upload(
                    $request->file('audio_file')
                        ->getRealPath(),
                    [
                        'folder' => 'music/audio',
                        'resource_type' => 'video'
                    ]
                );

            $data['audio_file'] =
                $audioUpload['secure_url'];

            $data['audio_public_id'] =
                $audioUpload['public_id'];
        }

        $music->update($data);

        return redirect()
            ->route('admin.music')
            ->with(
                'success',
                'Music updated ✨'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $music = Music::findOrFail($id);

        $cloudinary = app(Cloudinary::class);

        if (!empty($music->cover_public_id)) {

            try {

                $cloudinary
                    ->uploadApi()
                    ->destroy(
                        $music->cover_public_id
                    );

            } catch (\Exception $e) {
            }
        }

        if (!empty($music->audio_public_id)) {

            try {

                $cloudinary
                    ->uploadApi()
                    ->destroy(
                        $music->audio_public_id,
                        [
                            'resource_type' => 'video'
                        ]
                    );

            } catch (\Exception $e) {
            }
        }

        $music->delete();

        return redirect()
            ->route('admin.music')
            ->with(
                'success',
                'Music deleted'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | USER PAGE
    |--------------------------------------------------------------------------
    */

    public function userIndex()
    {
        $musics = Music::latest()->get();

        return view('user.music', compact('musics'));
    }
}