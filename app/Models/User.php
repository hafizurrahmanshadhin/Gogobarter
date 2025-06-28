<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject {
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array {
        return [
            'id'                   => 'integer',
            'name'                 => 'string',
            'email'                => 'string',
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'terms_and_conditions' => 'boolean',
            'avatar'               => 'string',
            'cover_photo'          => 'string',
            'date_of_birth'        => 'datetime',
            'otp_verified_at'      => 'datetime',
            'role'                 => 'string',
            'status'               => 'string',
            'remember_token'       => 'string',
            'created_at'           => 'datetime',
            'updated_at'           => 'datetime',
            'deleted_at'           => 'datetime',
        ];
    }

    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array {
        return [];
    }

    /**
     * When saving email, always lowercase
     */
    public function setEmailAttribute($value) {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * successor for avatar attribute
     *
     * @param mixed $url
     * @return string
     */
    public function getAvatarAttribute($url): string {
        if ($url) {
            if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
                return $url;
            } else {
                return asset('storage/' . $url);
            }
        } else {
            return asset('backend/images/users/user-dummy-img.jpg');
        }
    }

    public function products(): HasMany {
        return $this->hasMany(Product::class);
    }

    public function favorites(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'favorites')->withTimestamps();
    }
}
