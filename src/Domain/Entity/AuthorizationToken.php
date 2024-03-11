<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Override;
use SensitiveParameter;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthorizationToken extends AbstractToken
{
    public function __construct(
        private readonly UserInterface $user,
        private readonly string $firewallName,
        #[SensitiveParameter]
        private readonly string $secret,
    ) {
        parent::__construct($this->user->getRoles());

        if ($this->firewallName === '') {
            throw new InvalidArgumentException(message: 'Firewall name must not be empty.');
        }

        if ($this->secret === '') {
            throw new InvalidArgumentException(message: 'Secret must not be empty.');
        }

        $this->setUser($this->user);
    }

    public function getFirewallName(): string
    {
        return $this->firewallName;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @return array<int, mixed>
     */
    #[Override]
    public function __serialize(): array
    {
        return [$this->secret, $this->firewallName, parent::__serialize()];
    }

    /**
     * @param array&array<int, mixed> $data
     */
    #[Override]
    public function __unserialize(array $data): void
    {
        [$secret, $firewallName, $parentData] = $data;

        $this->secret = is_string($secret) ? $secret : '';
        $this->firewallName = is_string($firewallName) ? $firewallName : '';

        parent::__unserialize(data: is_array($parentData) ? $parentData : []);
    }
}
