<?php

namespace App\Traits;

use Cloudinary\Cloudinary;

trait CloudinaryUpload
{
    /*
    |--------------------------------------------------------------------------
    | UPLOAD
    |--------------------------------------------------------------------------
    */

    protected function uploadToCloudinary(
        $file,
        $folder
    ) {

        $cloudinary =
            app(Cloudinary::class);

        $upload = $cloudinary
            ->uploadApi()
            ->upload(
                $file->getRealPath(),
                [
                    'folder' => $folder,
                ]
            );

        return [

            'url' => $upload['secure_url'],

            'public_id' => $upload['public_id'],

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    protected function deleteFromCloudinary(
        $publicId
    ) {

        if (empty($publicId)) {
            return;
        }

        try {

            $cloudinary =
                app(Cloudinary::class);

            $cloudinary
                ->uploadApi()
                ->destroy(
                    $publicId
                );

        } catch (\Exception $e) {

            \Log::warning(
                'Cloudinary delete failed: '
                .$e->getMessage()
            );
        }
    }
}
