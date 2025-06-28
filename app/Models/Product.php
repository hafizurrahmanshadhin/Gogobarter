<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_category_id',
        'name',
        'images',
        'description',
        'condition',
        'status',
    ];

    protected $casts = [
        'id'                  => 'integer',
        'user_id'             => 'integer',
        'product_category_id' => 'integer',
        'images'              => 'array',
        'description'         => 'string',
        'condition'           => 'string',
        'status'              => 'string',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'deleted_at'          => 'datetime',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function favorites(): BelongsToMany {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
}
