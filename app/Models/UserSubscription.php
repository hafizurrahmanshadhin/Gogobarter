<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'status',
        'starts_at',
        'ends_at',
        'cancelled_at',
        'amount_paid',
        'payment_method',
        'transaction_id',
    ];

    protected $casts = [
        'starts_at'    => 'datetime',
        'ends_at'      => 'datetime',
        'cancelled_at' => 'datetime',
        'amount_paid'  => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function subscriptionPlan() {
        return $this->belongsTo(SubscriptionPlan::class);
    }
}
