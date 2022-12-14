<?php

namespace App\Models;

// use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider_id',
        'provider_name',
        'google_access_token_json'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function uploadAvatar($image, $extension = 'jpg')
    {
        if ($image == NULL) {
            return;
        }
        $this->removeAvatar();
        $filename = str_random(10) . '' . $extension;
        $store = Storage::disk('uploads')->put('uploads/avatars/' . $filename, $image);
        if (!$store) {
            $this->profile = NULL;
        } else {
            $this->profile = $filename;
        }
        $this->save();
        return 'uploads/avatars/' . $filename;

    }
    public function removeAvatar()
    {

        if ($this->profile != null && file_exists('uploads/avatars/' . $this->profile)) {
            Storage::disk('uploads')->delete('uploads/avatars/' . $this->profile);
        }

    }
    public function getAvatar()
    {
        if ($this->profile == null) {
            return '/img/no_avatar.jpg';

        }
        return '/uploads/avatars/' . $this->profile;
    }

}