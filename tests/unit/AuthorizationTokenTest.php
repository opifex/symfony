<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Security\AuthorizationToken;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

final class AuthorizationTokenTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->user = $this->createMock(originalClassName: UserInterface::class);
    }

    public function testSerializeAndUnserializeToken(): void
    {
        $token = new AuthorizationToken($this->user, firewallName: 'firewall', secret: 'secret');

        /** @var AuthorizationToken $restoredToken */
        $restoredToken = unserialize(serialize($token));

        $this->assertEquals($token->getRoleNames(), $restoredToken->getRoleNames());
        $this->assertEquals($token->getFirewallName(), $restoredToken->getFirewallName());
        $this->assertEquals($token->getSecret(), $restoredToken->getSecret());
        $this->assertEquals($token->getUserIdentifier(), $restoredToken->getUserIdentifier());
    }

    public function testCreateThrowsExceptionWithEmptyFirewallName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AuthorizationToken($this->user, firewallName: '', secret: 'secret');
    }

    public function testCreateThrowsExceptionWithEmptySecret(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new AuthorizationToken($this->user, firewallName: 'firewall', secret: '');
    }
}
