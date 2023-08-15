<?php

declare(strict_types=1);

namespace App\Application\Handler\GetAccountById;

use App\Domain\Contract\AccountRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetAccountByIdHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(GetAccountByIdQuery $message): GetAccountByIdResponse
    {
        $account = $this->accountRepository->findOneByUuid($message->uuid);

        return new GetAccountByIdResponse($account);
    }
}
