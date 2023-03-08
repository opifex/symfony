<?php

declare(strict_types=1);

namespace App\Application\Handler\Auth;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Message\Auth\GetSigninAccountInfoQuery;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\User\UserInterface;

#[AsMessageHandler(bus: MessageInterface::QUERY)]
class GetSigninAccountInfoHandler
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(GetSigninAccountInfoQuery $message): UserInterface
    {
        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedHttpException(
                message: 'An authentication exception occurred.',
            );
        }

        return $user;
    }
}
