<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'delivery_address',
        'payment_method',
        'total_amount',
        'status',
        'notes',
        'payment_status',
        'payment_gateway',
        'payment_reference',
        'paid_at',
        'tracking_number',
        'shipping_status',
        'shipping_provider',
        'discount_amount',
        'coupon_code',
        'latitude',
        'longitude',
        'ward_code',
        'district_id',
        'province_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
