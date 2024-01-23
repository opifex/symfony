<?php

declare(strict_types=1);

namespace App\Application\Handler\GetSigninAccount;

use App\Domain\Entity\Account;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[AsMessageHandler(bus: 'query.bus')]
final class GetSigninAccountHandler
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function __invoke(GetSigninAccountQuery $message): GetSigninAccountResponse
    {
        $account = $this->tokenStorage->getToken()?->getUser();

        if (!$account instanceof Account) {
            throw new AccessDeniedException(
                message: 'An authentication exception occurred.',
            );
        }

        return new GetSigninAccountResponse($account);
    }
}
