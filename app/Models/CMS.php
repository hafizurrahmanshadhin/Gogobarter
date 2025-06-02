<?php

namespace App\Models;

use App\Observers\Web\Backend\CMS\HomePageHeroSectionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CMS extends Model {
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'c_m_s';

    protected $fillable = [
        'section',
        'title',
        'sub_title',
        'content',
        'image',
        'items',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id'         => 'integer',
        'section'    => 'string',
        'title'      => 'string',
        'sub_title'  => 'string',
        'content'    => 'string',
        'image'      => 'string',
        'items'      => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot(): void {
        parent::boot();
        static::observe(HomePageHeroSectionObserver::class);
    }

    public function setItemsAttribute($value): void {
        if (is_array($value)) {
            $this->attributes['items'] = json_encode($value);
        } else {
            $this->attributes['items'] = null;
        }
    }

    public function getItemsAttribute($value): mixed {
        return is_array($value) ? $value : json_decode($value, true);
    }

    public function setSectionAttribute($value): void {
        $this->attributes['section'] = strtolower($value);
    }

    public function getSectionAttribute($value): string {
        return match ($value) {
            'hero' => 'Hero Section',
            'service'     => 'Service Section',
            'instruction' => 'Instruction Section',
            'trading'     => 'Trading Section',
            default       => ucfirst($value),
        };
    }

    public function getImageAttribute($value): mixed {
        if ($value) {
            if (strpos($value, 'http://') === 0 || strpos($value, 'https://') === 0) {
                return $value;
            } else {
                return asset('storage/' . $value);
            }
        }
        return asset('backend/images/users/user-dummy-img.jpg');
    }
}
