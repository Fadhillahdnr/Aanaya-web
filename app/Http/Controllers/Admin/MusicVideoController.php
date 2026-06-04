<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MusicVideo;
use Illuminate\Http\Request;
use Cloudinary\Cloudinary;

class MusicVideoController extends Controller
{
    private function uploadToCloudinary(
        $file,
        $folder,
        $resourceType = 'image'
    ) {
        $cloudinary = app(Cloudinary::class);

        $upload = $cloudinary
            ->uploadApi()
            ->upload(
                $file->getRealPath(),
                [
                    'folder' => $folder,
                    'resource_type' => $resourceType
                ]
            );

        return [

            'url' => $upload['secure_url'],

            'public_id' => $upload['public_id'],

        ];
    }
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $videos = MusicVideo::latest()->get();

        return view('admin.music-vidio', compact('videos'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.music-vidio-create');
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

            'video_file' =>
                'required|mimes:mp4,mov,avi,webm|max:512000',

            'thumbnail' =>
                'nullable|image',

            'description' =>
                'nullable',

        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD VIDEO
        |--------------------------------------------------------------------------
        */

        $videoUrl = null;
        $videoPublicId = null;

        if ($request->hasFile('video_file')) {

            $videoUpload =
                $this->uploadToCloudinary(
                    $request->file('video_file'),
                    'music-videos',
                    'video'
                );

            $videoUrl =
                $videoUpload['url'];

            $videoPublicId =
                $videoUpload['public_id'];
        }

        /*
        |--------------------------------------------------------------------------
        | UPLOAD THUMBNAIL
        |--------------------------------------------------------------------------
        */

        $thumbnailUrl = null;
        $thumbnailPublicId = null;

        if ($request->hasFile('thumbnail')) {

            $thumbnailUpload =
                $this->uploadToCloudinary(
                    $request->file('thumbnail'),
                    'music-video-thumbnails'
                );

            $thumbnailUrl =
                $thumbnailUpload['url'];

            $thumbnailPublicId =
                $thumbnailUpload['public_id'];
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE
        |--------------------------------------------------------------------------
        */

        MusicVideo::create([

            'title' => $request->title,

            'artist' => $request->artist,

            'thumbnail' => $thumbnailUrl,

            'thumbnail_public_id' =>
                $thumbnailPublicId,

            'video_file' => $videoUrl,

            'video_public_id' =>
                $videoPublicId,

            'description' =>
                $request->description,

            'is_featured' =>
                $request->is_featured
                ? true
                : false,

        ]);

        return redirect('/admin/mv')
            ->with(
                'success',
                'Music video uploaded'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(MusicVideo $mv)
    {
        $video = $mv;

        return view(
            'admin.music-vidio-edit',
            compact('video')
        );
    }
    
    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        MusicVideo $mv
    ) {
        $request->validate([

            'title' => 'required',

            'artist' => 'required',

            'video_file' =>
                'nullable|mimes:mp4,mov,avi,webm|max:512000',

            'thumbnail' =>
                'nullable|image|mimes:jpg,jpeg,png,webp',

            'description' =>
                'nullable',
        ]);

        $cloudinary = app(\Cloudinary\Cloudinary::class);

        /*
        |--------------------------------------------------------------------------
        | BASIC DATA
        |--------------------------------------------------------------------------
        */

        $data = [

            'title' => $request->title,

            'artist' => $request->artist,

            'description' => $request->description,

            'is_featured' =>
                $request->has('is_featured'),
        ];

        /*
        |--------------------------------------------------------------------------
        | REPLACE THUMBNAIL
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('thumbnail')) {

            if (!empty($mv->thumbnail_public_id)) {

                try {

                    $cloudinary
                        ->uploadApi()
                        ->destroy(
                            $mv->thumbnail_public_id
                        );

                } catch (\Exception $e) {

                    \Log::warning(
                        'Thumbnail delete failed: '
                        . $e->getMessage()
                    );
                }
            }

            $uploadThumbnail = $cloudinary
                ->uploadApi()
                ->upload(
                    $request
                        ->file('thumbnail')
                        ->getRealPath(),
                    [
                        'folder' =>
                            'music-videos/thumbnails'
                    ]
                );

            $data['thumbnail'] =
                $uploadThumbnail['secure_url'];

            $data['thumbnail_public_id'] =
                $uploadThumbnail['public_id'];
        }

        /*
        |--------------------------------------------------------------------------
        | REPLACE VIDEO
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('video_file')) {

            if (!empty($mv->video_public_id)) {

                try {

                    $cloudinary
                        ->uploadApi()
                        ->destroy(
                            $mv->video_public_id,
                            [
                                'resource_type' => 'video'
                            ]
                        );

                } catch (\Exception $e) {

                    \Log::warning(
                        'Video delete failed: '
                        . $e->getMessage()
                    );
                }
            }

            $uploadVideo = $cloudinary
                ->uploadApi()
                ->upload(
                    $request
                        ->file('video_file')
                        ->getRealPath(),
                    [
                        'folder' =>
                            'music-videos/videos',

                        'resource_type' =>
                            'video'
                    ]
                );

            $data['video_file'] =
                $uploadVideo['secure_url'];

            $data['video_public_id'] =
                $uploadVideo['public_id'];
        }

        /*
        |--------------------------------------------------------------------------
        | UPDATE
        |--------------------------------------------------------------------------
        */

        $mv->update($data);

        return redirect('/admin/mv')
            ->with(
                'success',
                'Music video updated successfully ✨'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(MusicVideo $mv)
    {
        $cloudinary = app(Cloudinary::class);

        /*
        |--------------------------------------------------------------------------
        | DELETE VIDEO
        |--------------------------------------------------------------------------
        */

        if (!empty($mv->video_public_id)) {

            try {

                $cloudinary
                    ->uploadApi()
                    ->destroy(
                        $mv->video_public_id,
                        [
                            'resource_type' => 'video'
                        ]
                    );

            } catch (\Exception $e) {

                \Log::warning(
                    'Video delete failed: '
                    . $e->getMessage()
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | DELETE THUMBNAIL
        |--------------------------------------------------------------------------
        */

        if (!empty($mv->thumbnail_public_id)) {

            try {

                $cloudinary
                    ->uploadApi()
                    ->destroy(
                        $mv->thumbnail_public_id
                    );

            } catch (\Exception $e) {

                \Log::warning(
                    'Thumbnail delete failed: '
                    . $e->getMessage()
                );
            }
        }

        $mv->delete();

        return redirect('/admin/mv')
            ->with(
                'success',
                'Music video deleted'
            );
    }
}