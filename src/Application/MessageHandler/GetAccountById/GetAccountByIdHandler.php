<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountById;

use App\Domain\Contract\AccountRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountByIdHandler
{
    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
    ) {
    }

    public function __invoke(GetAccountByIdRequest $message): GetAccountByIdResponse
    {
        $account = $this->accountRepository->findOneByUuid($message->uuid);

        return GetAccountByIdResponse::create($account);
    }
}
