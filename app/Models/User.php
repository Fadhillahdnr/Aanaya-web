<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [

        'name',
        'email',
        'password',
        'profile_photo',
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
            // Keep locally uploaded photos on the current host. Using asset()
            // here can point to the wrong port when APP_URL differs from the
            // URL used to access the application during local development.
            return '/storage/' . ltrim($this->profile_photo, '/');
        }

        if ($this->avatar) {

            return $this->avatar;
        }

        return asset(
            'assets/default-avatar.png'
        );
    }
}
