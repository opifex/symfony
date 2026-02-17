<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountById;

use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountByIdQueryHandler
{
    public function __construct(
        private readonly AccountEntityRepositoryInterface $accountEntityRepository,
    ) {
    }

    public function __invoke(GetAccountByIdQuery $query): GetAccountByIdQueryResult
    {
        $account = $this->accountEntityRepository->findOneById($query->id);

        return GetAccountByIdQueryResult::success($account);
    }
}
