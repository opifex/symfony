<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Application\Service\AccountManager;
use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\AccountAction;
use App\Domain\Exception\Account\AccountActionFailedException;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Message\Account\ApplyAccountActionCommand;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::COMMAND)]
class ApplyAccountActionHandler
{
    public function __construct(private AccountManager $accountManager)
    {
    }

    public function __invoke(ApplyAccountActionCommand $message): void
    {
        try {
            $action = AccountAction::from($message->action);
            $this->accountManager->applyAction($message->uuid, $action);
        } catch (AccountNotFoundException $e) {
            throw new NotFoundHttpException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        } catch (AccountActionFailedException $e) {
            throw new BadRequestHttpException(
                message: 'Provided action cannot be applied to account.',
                previous: $e,
            );
        }
    }
}
