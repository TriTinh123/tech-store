<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'favorite_categories',
        'price_range',
        'viewed_products',
        'purchased_products',
        'total_purchases',
        'average_spending',
        'shopping_frequency',
        'last_purchase_at',
    ];

    protected $casts = [
        'favorite_categories' => 'json',
        'price_range' => 'json',
        'viewed_products' => 'json',
        'purchased_products' => 'json',
        'last_purchase_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
