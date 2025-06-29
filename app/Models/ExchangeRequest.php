<?php

namespace App\Models;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExchangeRequest extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'requester_id',
        'requested_product_id',
        'offered_product_id',
        'message',
        'status',
    ];

    protected $casts = [
        'id'                   => 'integer',
        'requester_id'         => 'integer',
        'requested_product_id' => 'integer',
        'offered_product_id'   => 'integer',
        'message'              => 'string',
        'status'               => 'string',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
        'deleted_at'           => 'datetime',
    ];

    public function requester(): BelongsTo {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function requestedProduct(): BelongsTo {
        return $this->belongsTo(Product::class, 'requested_product_id');
    }

    public function offeredProduct(): BelongsTo {
        return $this->belongsTo(Product::class, 'offered_product_id');
    }
}
