<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\SubscriptionStatus;
use App\Enums\PaymentStatus;

class Subscription extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'status',
        'payment_status',
        'starts_at',
        'ends_at',
        'canceled_at',
        'amount',
        'transaction_id',
        'payment_reference',
        'payment_method',
        'phone_number',
        'payment_proof_url',
        'payment_proof_name',
        'admin_notes',
        'notes',
        'user_id',
        'subscription_plan_id',
        'cancelled_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'string',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'canceled_at' => 'datetime',
        'amount' => 'float',
        'payment_status' => 'string',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Get the subscription plan associated with the subscription.
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Get the user that owns the subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan associated with the subscription.
     * This is an alias of plan() for backward compatibility.
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->plan();
    }

}
