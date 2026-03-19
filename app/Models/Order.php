<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $order_number
 * @property string $customer_name
 * @property string $customer_email
 * @property string $customer_phone
 * @property string $delivery_address
 * @property string $payment_method
 * @property float $total_amount
 * @property string $status
 * @property string|null $notes
 * @property string $payment_status
 * @property string|null $payment_gateway
 * @property string|null $payment_reference
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property string|null $tracking_number
 * @property string $shipping_status
 * @property string|null $shipping_provider
 * @property float $discount_amount
 * @property string|null $coupon_code
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $ward_code
 * @property int|null $district_id
 * @property int|null $province_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
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
        'paid_at'    => 'datetime',
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
