<?php

declare(strict_types=1);

namespace App\Application\Handler\Auth;

use App\Domain\Message\Auth\SigninIntoAccountCommand;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

#[AsMessageHandler(bus: 'command.bus')]
final class SigninIntoAccountHandler
{
    public function __construct(private Security $security)
    {
    }

    public function __invoke(SigninIntoAccountCommand $message): void
    {
        $token = $this->security->getToken();

        if (!$token instanceof TokenInterface) {
            throw new AccessDeniedHttpException(
                message: 'An authentication exception occurred.',
            );
        }
    }
}
