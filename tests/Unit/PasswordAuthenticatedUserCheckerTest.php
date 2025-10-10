<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Account\AccountRole;
use App\Infrastructure\Security\AuthenticatedUser\PasswordAuthenticatedUser;
use App\Infrastructure\Security\AuthenticatedUser\PasswordAuthenticatedUserChecker;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

final class PasswordAuthenticatedUserCheckerTest extends TestCase
{
    private UserInterface&MockObject $user;

    #[Override]
    protected function setUp(): void
    {
        $this->user = $this->createMock(type: UserInterface::class);
    }

    public function testCheckPostAuthWithBlockedAccount(): void
    {
        $accountUserChecker = new PasswordAuthenticatedUserChecker();
        $accountUser = new PasswordAuthenticatedUser(
            userIdentifier: Uuid::v7()->hash(),
            password: 'password4#account',
            roles: [AccountRole::User],
            enabled: false,
        );

        $this->expectException(LockedException::class);

        $accountUserChecker->checkPostAuth($accountUser);
    }

    public function testCheckPostAuthWithNonAccountUser(): void
    {
        $accountUserChecker = new PasswordAuthenticatedUserChecker();
        $accountUserChecker->checkPostAuth($this->user);

        $this->expectNotToPerformAssertions();
    }

    public function testCheckPostAuthWithVerifiedAccount(): void
    {
        $accountUserChecker = new PasswordAuthenticatedUserChecker();
        $accountUser = new PasswordAuthenticatedUser(
            userIdentifier: Uuid::v7()->hash(),
            password: 'password4#account',
            roles: [AccountRole::User],
            enabled: true,
        );
        $accountUserChecker->checkPostAuth($accountUser);

        $this->expectNotToPerformAssertions();
    }
}
