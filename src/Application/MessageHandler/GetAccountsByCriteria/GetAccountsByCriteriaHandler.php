<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Entity\AccountSearchCriteria;
use App\Domain\Entity\SearchPagination;
use App\Domain\Entity\SearchSorting;
use App\Domain\Entity\SortingOrder;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetAccountsByCriteriaHandler
{
    public function __construct(private readonly AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(GetAccountsByCriteriaRequest $message): GetAccountsByCriteriaResponse
    {
        $sorting = new SearchSorting($message->sort, SortingOrder::fromValue($message->order));
        $pagination = new SearchPagination($message->limit, $message->offset);
        $searchCriteria = new AccountSearchCriteria($message->email, $message->status, $sorting, $pagination);

        $accountSearchResult = $this->accountRepository->findByCriteria($searchCriteria);

        return new GetAccountsByCriteriaResponse($accountSearchResult);
    }
}
