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
                'initial_marking' => AccountStatus::CREATED,
                'places' => [
                    AccountStatus::ACTIVATED,
                    AccountStatus::BLOCKED,
                    AccountStatus::CREATED,
                    AccountStatus::REGISTERED,
                ],
                'transitions' => [
                    AccountAction::ACTIVATE => [
                        'from' => AccountStatus::REGISTERED,
                        'to' => AccountStatus::ACTIVATED,
                    ],
                    AccountAction::BLOCK => [
                        'from' => AccountStatus::ACTIVATED,
                        'to' => AccountStatus::BLOCKED,
                    ],
                    AccountAction::REGISTER => [
                        'from' => AccountStatus::CREATED,
                        'to' => AccountStatus::REGISTERED,
                    ],
                    AccountAction::UNBLOCK => [
                        'from' => AccountStatus::BLOCKED,
                        'to' => AccountStatus::ACTIVATED,
                    ],
                ],
            ],
        ],
    ]);
};
