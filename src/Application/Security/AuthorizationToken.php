<?php

declare(strict_types=1);

namespace App\Application\Security;

use App\Domain\Contract\AuthorizationTokenInterface;
use Override;
use SensitiveParameter;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

final class AuthorizationToken extends AbstractToken implements AuthorizationTokenInterface
{
    private string $firewallName;

    private string $secret;

    public function __construct(UserInterface $user, string $firewallName, #[SensitiveParameter] string $secret)
    {
        parent::__construct($user->getRoles());

        if ($firewallName === '') {
            throw new InvalidArgumentException(message: 'Firewall name must not be empty.');
        }

        if ($secret === '') {
            throw new InvalidArgumentException(message: 'Secret must not be empty.');
        }

        $this->firewallName = $firewallName;
        $this->secret = $secret;

        $this->setUser($user);
    }

    public function getFirewallName(): string
    {
        return $this->firewallName;
    }

    #[Override]
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