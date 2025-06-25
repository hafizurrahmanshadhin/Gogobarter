<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class ProductCategory extends Model {
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'product_categories';

    protected $fillable = [
        'id',
        'name',
        'status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'id'           => 'integer',
        'name'         => 'string',
        'platform_fee' => 'integer',
        'status'       => 'string',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    public function products(): HasMany {
        return $this->hasMany(Product::class, 'product_category_id');
    }
}
