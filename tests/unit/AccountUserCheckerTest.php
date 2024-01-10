<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Factory\AccountFactory;
use App\Application\Security\AccountUserChecker;
use App\Domain\Entity\AccountStatus;
use App\Domain\Entity\LocaleCode;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserInterface;

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
        $account = AccountFactory::createUserAccount(email: 'email@example.com', locale: LocaleCode::EN);

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
        $account = AccountFactory::createUserAccount(email: 'email@example.com', locale: LocaleCode::EN);
        $account->setStatus(status: AccountStatus::VERIFIED);
        $accountUserChecker->checkPostAuth($account);

        $this->expectNotToPerformAssertions();
    }
}
