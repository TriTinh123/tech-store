<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int             $id
 * @property string          $code
 * @property string          $type
 * @property float           $value
 * @property string|null     $description
 * @property float           $min_order_amount
 * @property float|null      $max_discount
 * @property int|null        $usage_limit
 * @property int             $used_count
 * @property bool            $is_active
 * @property \Carbon\Carbon|null $expires_at
 * @property \Carbon\Carbon|null $valid_from
 * @property \Carbon\Carbon|null $valid_to
 * @property \Carbon\Carbon  $created_at
 * @property \Carbon\Carbon  $updated_at
 */
class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'description',
        'min_order_amount', 'max_discount',
        'usage_limit', 'used_count', 'is_active',
        'expires_at', 'valid_from', 'valid_to',
    ];

    protected $casts = [
        'expires_at'       => 'datetime',
        'valid_from'       => 'datetime',
        'valid_to'         => 'datetime',
        'is_active'        => 'boolean',
        'value'            => 'float',
        'min_order_amount' => 'float',
        'max_discount'     => 'float',
    ];

    /** Calculate discount amount for a given order total */
    public function calcDiscount(float $total): float
    {
        if ($this->type === 'percentage') {
            $discount = $total * ($this->value / 100);
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
        } else {
            $discount = $this->value;
        }
        return min($discount, $total);
    }

    /** Check if coupon is usable */
    public function isValid(float $total): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;
        if ($total < $this->min_order_amount) return false;
        return true;
    }
}
