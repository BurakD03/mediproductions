<?php

declare(strict_types=1);

namespace App\Entity\Subscription;

interface SubscriptionStates
{
    public const STATE_ACTIVE = 'active';
    public const STATE_PAUSE = 'pause';
    public const STATE_CANCELLED = 'cancelled';
    public const STATE_COMPLETED = 'completed';
}