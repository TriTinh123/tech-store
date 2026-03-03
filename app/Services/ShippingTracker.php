<?php

namespace App\Services;

class ShippingTracker
{
    public static function getProvider($providerName)
    {
        return match ($providerName) {
            'GHN' => new GHNShippingProvider,
            'GRAB' => new GrabShippingProvider,
            default => throw new \Exception('Unsupported shipping provider'),
        };
    }

    public static function track($trackingNumber, $provider)
    {
        $shippingProvider = self::getProvider($provider);

        return $shippingProvider->getTrackingInfo($trackingNumber);
    }

    public static function create($order, $provider = 'GHN')
    {
        $shippingProvider = self::getProvider($provider);

        return $shippingProvider->createShipment($order);
    }

    public static function cancel($trackingNumber, $provider)
    {
        $shippingProvider = self::getProvider($provider);

        return $shippingProvider->cancelShipment($trackingNumber);
    }

    public static function estimate($address, $provider = 'GHN')
    {
        $shippingProvider = self::getProvider($provider);

        return $shippingProvider->estimateDelivery($address);
    }
}
