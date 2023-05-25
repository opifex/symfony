<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Domain\Exception\AccountNotFoundException;
use App\Domain\Message\GetAccountByIdQuery;
use App\Domain\Response\GetAccountByIdResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetAccountByIdHandler
{
    public function __construct(private AccountRepositoryInterface $accountRepository)
    {
    }

    public function __invoke(GetAccountByIdQuery $message): GetAccountByIdResponse
    {
        try {
            $account = $this->accountRepository->findOneByUuid($message->uuid);
        } catch (AccountNotFoundException $e) {
            throw new NotFoundHttpException(
                message: 'Account with provided identifier not found.',
                previous: $e,
            );
        }

        return new GetAccountByIdResponse($account);
    }
}
