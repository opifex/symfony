<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Security\AccountUserChecker;
use App\Domain\Entity\Account;
use App\Domain\Entity\AccountStatus;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;

final class AccountUserCheckerTest extends Unit
{
    private AccountUserChecker $accountUserChecker;

    protected function setUp(): void
    {
        $this->accountUserChecker = new AccountUserChecker();
    }

    public function testCheckPostAuthWithBlockedAccount(): void
    {
        $this->expectException(CustomUserMessageAccountStatusException::class);

        $this->accountUserChecker->checkPostAuth(new Account(email: 'email@example.com'));
    }

    /**
     * @throws Exception
     */
    public function testCheckPostAuthWithNonAccountUser(): void
    {
        $user = $this->createMock(originalClassName: UserInterface::class);

        $this->accountUserChecker->checkPostAuth($user);

        $this->expectNotToPerformAssertions();
    }

    public function testCheckPostAuthWithVerifiedAccount(): void
    {
        $account = new Account(email: 'email@example.com');
        $account->setStatus(status: AccountStatus::VERIFIED);

        $this->accountUserChecker->checkPostAuth($account);

        $this->expectNotToPerformAssertions();
    }
}
