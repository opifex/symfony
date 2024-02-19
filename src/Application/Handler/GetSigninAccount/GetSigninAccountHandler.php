<?php

declare(strict_types=1);

namespace App\Application\Handler\GetSigninAccount;

use App\Domain\Contract\AccountAuthorizationFetcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetSigninAccountHandler
{
    public function __construct(private AccountAuthorizationFetcherInterface $accountAuthorizationFetcher)
    {
    }

    public function __invoke(GetSigninAccountQuery $message): GetSigninAccountResponse
    {
        $account = $this->accountAuthorizationFetcher->fetchAccount();

        return new GetSigninAccountResponse($account);
    }
}
