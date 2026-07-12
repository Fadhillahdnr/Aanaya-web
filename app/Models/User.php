<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [

        'name',
        'email',
        'password',
        'profile_photo',
        'profile_photo_public_id',
        'role',
        'google_id',
        'avatar',

    ];

    protected $hidden = [

        'password',
        'remember_token',

    ];

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',
            'password' => 'hashed',

        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ROLE CHECK
    |--------------------------------------------------------------------------
    */

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->profile_photo) {
            if (filter_var($this->profile_photo, FILTER_VALIDATE_URL)) {
                return $this->profile_photo;
            }

            // A database record can outlive a local file after a deploy or
            // container rebuild. Only render it when the file still exists,
            // otherwise continue to the Google avatar fallback below.
            if (Storage::disk('public')->exists($this->profile_photo)) {
                return '/storage/'.ltrim($this->profile_photo, '/');
            }
        }

        if ($this->avatar) {

            return $this->avatar;
        }

        return asset(
            'assets/default-avatar.png'
        );
    }
}
