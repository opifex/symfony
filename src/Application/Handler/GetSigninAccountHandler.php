<?php

declare(strict_types=1);

namespace App\Application\Handler;

use App\Domain\Entity\Account;
use App\Domain\Message\GetSigninAccountQuery;
use App\Domain\Response\GetSigninAccountResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final class GetSigninAccountHandler
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(GetSigninAccountQuery $message): GetSigninAccountResponse
    {
        $account = $this->security->getUser();

        if (!$account instanceof Account) {
            throw new AccessDeniedHttpException(
                message: 'An authentication exception occurred.',
            );
        }

        return new GetSigninAccountResponse($account);
    }
}
