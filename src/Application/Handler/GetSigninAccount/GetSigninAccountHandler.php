<?php

declare(strict_types=1);

namespace App\Application\Handler\GetSigninAccount;

use App\Domain\Contract\AccountInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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

        if (!$account instanceof AccountInterface) {
            throw new AccessDeniedHttpException(
                message: 'An authentication exception occurred.',
            );
        }

        return new GetSigninAccountResponse($account);
    }
}
