<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\PayPal\RemoteEvent;

use Symfony\Component\RemoteEvent\RemoteEvent;

final class PayPalPaymentCaptureEvent extends RemoteEvent
{
    // A payment capture completes.
    public const string COMPLETED = 'completed';
    // A payment capture is declined.
    public const string DECLINED = 'declined';
    // The state of a payment capture changes to pending.
    public const string PENDING = 'pending';
    // A merchant refunds a payment capture.
    public const string REFUNDED = 'refunded';
    // PayPal reverses a payment capture.
    public const string REVERSED = 'reversed';
}
