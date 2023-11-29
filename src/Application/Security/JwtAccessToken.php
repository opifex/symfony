<?php

declare(strict_types=1);

namespace App\Application\Security;

use Override;
use SensitiveParameter;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Exception\TokenNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

final class JwtAccessToken extends AbstractToken
{
    public function __construct(UserInterface $user, #[SensitiveParameter] string $accessToken)
    {
        parent::__construct($user->getRoles());

        $this->setAttribute(name: 'token', value: $accessToken);
        $this->setUser($user);
    }

    #[Override]
    public function __toString(): string
    {
        $token = $this->getAttribute(name: 'token');

        return is_string($token) ? $token : throw new TokenNotFoundException();
    }
}
