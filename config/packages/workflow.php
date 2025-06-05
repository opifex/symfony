<?php

declare(strict_types=1);

use App\Domain\Model\Account;
use App\Domain\Model\AccountAction;
use App\Domain\Model\AccountStatus;
use App\Infrastructure\Workflow\AccountMarkingStore;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'framework', config: [
        'workflows' => [
            'account' => [
                'type' => 'state_machine',
                'audit_trail' => [
                    'enabled' => '%kernel.debug%',
                ],
                'marking_store' => [
                    'service' => AccountMarkingStore::class,
                ],
                'supports' => [
                    Account::class,
                ],
                'initial_marking' => AccountStatus::Created->value,
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
    ]);
};
