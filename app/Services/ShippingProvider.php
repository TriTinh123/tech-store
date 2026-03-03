<?php

namespace App\Services;

abstract class ShippingProvider
{
    protected string $apiKey;

    protected string $apiUrl;

    abstract public function getTrackingInfo($trackingNumber);

    abstract public function createShipment($order);

    abstract public function cancelShipment($trackingNumber);

    abstract public function estimateDelivery($deliveryAddress);
}
