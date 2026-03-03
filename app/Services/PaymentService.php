<?php

namespace App\Services;

use App\Models\Order;

abstract class PaymentService
{
    protected Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    abstract public function initiate();

    abstract public function verify($paymentId);

    abstract public function getPaymentDetails();
}
