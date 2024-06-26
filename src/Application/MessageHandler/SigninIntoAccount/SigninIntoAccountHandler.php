<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use App\Domain\Contract\AccountAuthorizationFetcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SigninIntoAccountHandler
{
    public function __construct(private AccountAuthorizationFetcherInterface $accountAuthorizationFetcher)
    {
    }

    public function __invoke(SigninIntoAccountRequest $message): SigninIntoAccountResponse
    {
        $token = $this->accountAuthorizationFetcher->fetchToken();

        return new SigninIntoAccountResponse($token);
    }
}
