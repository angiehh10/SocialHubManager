<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Illuminate\Support\Facades\Storage; 
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{

    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
     use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $appends = [
        'profile_photo_url',
    ];

     public function getProfilePhotoUrlAttribute(): string
    {
        $path = $this->profile_photo_path;

        if (is_string($path) && preg_match('#^https?://#i', $path)) {
            return $path;
        }

        if (! empty($path)) {
            return Storage::disk($this->profilePhotoDisk())->url($path);
        }

        return $this->defaultProfilePhotoUrl();
    }

     public function socialConnections()
    {
        return $this->hasMany(\App\Models\SocialConnection::class);
    }

    public function socialConnection(string $provider)
    {
        return $this->socialConnections()->where('provider', $provider)->first();
    }

}
