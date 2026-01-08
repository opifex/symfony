<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\Query\GetAccountById;

use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use App\Domain\Account\Exception\AccountNotFoundException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountByIdQueryHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
    ) {
    }

    public function __invoke(GetAccountByIdQuery $request): GetAccountByIdQueryResult
    {
        $account = $this->accountEntityRepository->findOneById($request->id)
            ?? throw AccountNotFoundException::create();

        return GetAccountByIdQueryResult::success($account);
    }
}
