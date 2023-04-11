<?php

declare(strict_types=1);

namespace App\Application\Handler\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Contract\Repository\AccountRepositoryInterface;
use App\Domain\Entity\Account\Account;
use App\Domain\Exception\AccountNotFoundException;
use App\Domain\Message\Account\GetAccountByIdQuery;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::QUERY)]
class GetAccountByIdHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(GetAccountByIdQuery $message): Account
    {
        try {
            return $this->accountRepository->findOneByUuid($message->uuid);
        } catch (AccountNotFoundException $e) {
            throw new NotFoundHttpException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }
    }
}
