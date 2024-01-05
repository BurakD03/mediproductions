<?php

declare(strict_types=1);

namespace App\EventListener\Subscription\Transitions;

final class SubscriptionRecurringTransitions
{
    public const GRAPH_MANUAL = 'subscription_manual';

    public const TRANSITION_ACTIVATE = 'activate';

    public const TRANSITION_CANCEL = 'cancelled';

    public const TRANSITION_COMPLETE = 'completed';

    private function __construct()
    {
    }
}