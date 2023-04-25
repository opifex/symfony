<?php

declare(strict_types=1);

namespace App\Application\Handler\Auth;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Entity\Account\Account;
use App\Domain\Message\Auth\GetSigninAccountInfoQuery;
use App\Domain\Response\GetSigninAccountInfoResponse;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: MessageInterface::QUERY)]
final class GetSigninAccountInfoHandler
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(GetSigninAccountInfoQuery $message): GetSigninAccountInfoResponse
    {
        $account = $this->security->getUser();

        if (!$account instanceof Account) {
            throw new AccessDeniedHttpException(
                message: 'An authentication exception occurred.',
            );
        }

        return new GetSigninAccountInfoResponse($account);
    }
}
