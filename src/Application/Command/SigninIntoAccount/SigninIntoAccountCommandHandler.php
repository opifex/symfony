<?php

declare(strict_types=1);

namespace App\Application\Command\SigninIntoAccount;

use App\Application\Contract\AuthorizationTokenStorageInterface;
use App\Application\Contract\JwtAccessTokenIssuerInterface;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Contract\AccountEntityRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SigninIntoAccountCommandHandler
{
    public function __construct(
        private AccountEntityRepositoryInterface $accountEntityRepository,
        private AuthorizationTokenStorageInterface $authorizationTokenStorage,
        private JwtAccessTokenIssuerInterface $jwtAccessTokenIssuer,
    ) {
    }

    public function __invoke(SigninIntoAccountCommand $command): SigninIntoAccountCommandResult
    {
        $userIdentifier = $this->authorizationTokenStorage->getUserIdentifier();

        $accountId = AccountIdentifier::fromString($userIdentifier);

        $account = $this->accountEntityRepository->findOneById($accountId);

        $expiresIn = $this->jwtAccessTokenIssuer->lifetime();
        $accessToken = $this->jwtAccessTokenIssuer->issue(
            userIdentifier: $account->id->toString(),
            userRoles: $account->roles->toArray(),
        );

        return SigninIntoAccountCommandResult::success($accessToken, $expiresIn);
    }
}
