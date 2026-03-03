<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'max_discount',
        'min_order_amount',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_to',
        'is_active',
    ];

    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid()
    {
        $now = now();

        return $this->is_active
            && $now >= $this->valid_from
            && $now <= $this->valid_to
            && ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    public function calculateDiscount($amount)
    {
        if ($this->type === 'fixed') {
            return min($this->value, $this->max_discount ?? $this->value);
        } else {
            $discount = ($amount * $this->value) / 100;

            return min($discount, $this->max_discount ?? $discount);
        }
    }
}
