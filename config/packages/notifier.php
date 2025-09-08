<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return function (FrameworkConfig $framework): void {
    $framework->notifier()->channelPolicy(name: 'urgent', value: ['email']);
    $framework->notifier()->channelPolicy(name: 'high', value: ['email']);
    $framework->notifier()->channelPolicy(name: 'medium', value: ['email']);
    $framework->notifier()->channelPolicy(name: 'low', value: ['email']);
    $framework->notifier()->adminRecipient()->email(value: 'admin@example.com');
};
