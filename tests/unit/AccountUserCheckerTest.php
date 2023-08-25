<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Factory\AccountFactory;
use App\Application\Security\AccountUserChecker;
use App\Domain\Entity\AccountStatus;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;

final class AccountUserCheckerTest extends Unit
{
    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->user = $this->createMock(originalClassName: UserInterface::class);

        $this->accountUserChecker = new AccountUserChecker();
    }

    public function testCheckPostAuthWithBlockedAccount(): void
    {
        $account = AccountFactory::createUserAccount(email: 'email@example.com');

        $this->expectException(CustomUserMessageAccountStatusException::class);

        $this->accountUserChecker->checkPostAuth($account);
    }

    public function testCheckPostAuthWithNonAccountUser(): void
    {
        $this->accountUserChecker->checkPostAuth($this->user);

        $this->expectNotToPerformAssertions();
    }

    public function testCheckPostAuthWithVerifiedAccount(): void
    {
        $account = AccountFactory::createUserAccount(email: 'email@example.com');
        $account->setStatus(status: AccountStatus::VERIFIED);

        $this->accountUserChecker->checkPostAuth($account);

        $this->expectNotToPerformAssertions();
    }
}
