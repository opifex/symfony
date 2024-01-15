<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Security\JwtAccessToken;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;
use Symfony\Component\Security\Core\User\UserInterface;

final class JwtAccessTokenTest extends Unit
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
        $token = new JwtAccessToken($this->user, firewallName: 'firewall', secret: 'secret');

        /** @var JwtAccessToken $restoredToken */
        $restoredToken = unserialize(serialize($token));

        $this->assertEquals($token->getRoleNames(), $restoredToken->getRoleNames());
        $this->assertEquals($token->getSecret(), $restoredToken->getSecret());
        $this->assertEquals($token->getUserIdentifier(), $restoredToken->getUserIdentifier());
    }

    public function testCreateThrowsExceptionWithEmptyFirewallName(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new JwtAccessToken($this->user, firewallName: '', secret: 'secret');
    }

    public function testCreateThrowsExceptionWithEmptySecret(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new JwtAccessToken($this->user, firewallName: 'firewall', secret: '');
    }
}
