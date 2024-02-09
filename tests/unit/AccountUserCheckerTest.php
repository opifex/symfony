<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Security\AccountUserChecker;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountStatus;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

final class AccountUserCheckerTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->user = $this->createMock(originalClassName: UserInterface::class);
    }

    public function testCheckPostAuthWithBlockedAccount(): void
    {
        $accountUserChecker = new AccountUserChecker();
        $account = new Account(uuid: Uuid::v7()->toRfc4122(), email: 'email@example.com', locale: 'en');

        $this->expectException(CustomUserMessageAccountStatusException::class);

        $accountUserChecker->checkPostAuth($account);
    }

    public function testCheckPostAuthWithNonAccountUser(): void
    {
        $accountUserChecker = new AccountUserChecker();
        $accountUserChecker->checkPostAuth($this->user);

        $this->expectNotToPerformAssertions();
    }

    public function testCheckPostAuthWithVerifiedAccount(): void
    {
        $accountUserChecker = new AccountUserChecker();
        $account = new Account(
            uuid: Uuid::v7()->toRfc4122(),
            email: 'email@example.com',
            locale: 'en',
            status: AccountStatus::ACTIVATED,
        );
        $accountUserChecker->checkPostAuth($account);

        $this->expectNotToPerformAssertions();
    }
}
