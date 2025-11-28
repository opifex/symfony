<?php

declare(strict_types=1);

use App\Domain\Account\Account;
use App\Domain\Account\AccountAction;
use App\Domain\Account\AccountStatus;
use App\Infrastructure\Workflow\AccountMarkingStore;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;

return App::config([
    'framework' => [
        'workflows' => [
            'enabled' => true,
            'workflows' => [
                'account' => [
                    'type' => 'state_machine',
                    'supports' => [Account::class],
                    'initial_marking' => AccountStatus::Created->value,
                    'audit_trail' => [
                        'enabled' => '%kernel.debug%',
                    ],
                    'marking_store' => [
                        'service' => AccountMarkingStore::class,
                    ],
                    'places' => [
                        AccountStatus::Activated->value,
                        AccountStatus::Blocked->value,
                        AccountStatus::Created->value,
                        AccountStatus::Registered->value,
                    ],
                    'transitions' => [
                        AccountAction::Activate->value => [
                            'from' => AccountStatus::Registered->value,
                            'to' => AccountStatus::Activated->value,
                        ],
                        AccountAction::Block->value => [
                            'from' => AccountStatus::Activated->value,
                            'to' => AccountStatus::Blocked->value,
                        ],
                        AccountAction::Register->value => [
                            'from' => AccountStatus::Created->value,
                            'to' => AccountStatus::Registered->value,
                        ],
                        AccountAction::Unblock->value => [
                            'from' => AccountStatus::Blocked->value,
                            'to' => AccountStatus::Activated->value,
                        ],
                    ],
                ],
            ],
        ],
    ],
]);
