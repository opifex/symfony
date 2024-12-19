<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetSigninAccount;

use App\Domain\Contract\AccountTokenStorageInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetSigninAccountHandler
{
    public function __construct(private readonly AccountTokenStorageInterface $accountTokenStorage)
    {
    }

    public function __invoke(GetSigninAccountRequest $message): GetSigninAccountResponse
    {
        $account = $this->accountTokenStorage->getAccount();

        return new GetSigninAccountResponse($account);
    }
}
