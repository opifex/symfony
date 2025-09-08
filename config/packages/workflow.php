<?php

declare(strict_types=1);

use App\Domain\Model\Account;
use App\Domain\Model\AccountAction;
use App\Domain\Model\AccountStatus;
use App\Infrastructure\Workflow\AccountMarkingStore;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $accountWorkflow = $framework->workflows()->workflows('account')
        ->type(value: 'state_machine')
        ->supports([Account::class])
        ->initialMarking(AccountStatus::Created->value);

    $accountWorkflow->auditTrail(['enabled' => '%kernel.debug%']);
    $accountWorkflow->markingStore()->service(value: AccountMarkingStore::class);

    $accountWorkflow->place()->name(AccountStatus::Activated->value);
    $accountWorkflow->place()->name(AccountStatus::Blocked->value);
    $accountWorkflow->place()->name(AccountStatus::Created->value);
    $accountWorkflow->place()->name(AccountStatus::Registered->value);

    $accountWorkflow->transition()->name(AccountAction::Activate->value)
        ->from(AccountStatus::Registered->value)
        ->to(AccountStatus::Activated->value);

    $accountWorkflow->transition()->name(AccountAction::Block->value)
        ->from(AccountStatus::Activated->value)
        ->to(AccountStatus::Blocked->value);

    $accountWorkflow->transition()->name(AccountAction::Register->value)
        ->from(AccountStatus::Created->value)
        ->to(AccountStatus::Registered->value);

    $accountWorkflow->transition()->name(AccountAction::Unblock->value)
        ->from(AccountStatus::Blocked->value)
        ->to(AccountStatus::Activated->value);
};
