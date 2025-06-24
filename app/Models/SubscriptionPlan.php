<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model {
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscription_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'billing_interval',
        'price',
        'currency',
        'description',
        'features',
        'is_recommended',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'id'               => 'integer',
        'name'             => 'string',
        'billing_interval' => 'string',
        'price'            => 'decimal:2',
        'currency'         => 'string',
        'description'      => 'string',
        'features'         => 'array',
        'is_recommended'   => 'boolean',
        'status'           => 'string',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];
}
