<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Domain\Account\Account;
use App\Domain\Account\AccountIdentifier;
use App\Domain\Account\Event\AccountRegisteredEvent;
use App\Domain\Account\Exception\AccountInvalidActionException;
use App\Domain\Foundation\ValueObject\EmailAddress;
use App\Domain\Foundation\ValueObject\PasswordHash;
use App\Domain\Localization\LocaleCode;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class AccountTest extends TestCase
{
    protected function setUp(): void
    {
        $passwordHash = '$2y$12$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ01234';
        $this->account = Account::create(
            id: AccountIdentifier::fromString(uuid: '00000000-0000-6000-8000-000000000000'),
            email: EmailAddress::fromString(email: 'email@example.com'),
            password: PasswordHash::fromString($passwordHash),
            locale: LocaleCode::EnUs,
        );
    }

    public function testRegisterThrowsWhenNotInCreatedStatus(): void
    {
        $registered = $this->account->register();

        $this->expectException(AccountInvalidActionException::class);

        (void) $registered->register();
    }

    public function testRegisterRaisesAccountRegisteredEvent(): void
    {
        self::assertSame([], $this->account->releaseEvents());

        $registered = $this->account->register();
        $events = $registered->releaseEvents();

        self::assertCount(expectedCount: 1, haystack: $events);
        self::assertInstanceOf(expected: AccountRegisteredEvent::class, actual: $events[0]);
        self::assertSame($registered->email, $events[0]->account->email);
    }

    public function testActivateThrowsWhenNotInRegisteredStatus(): void
    {
        $this->expectException(AccountInvalidActionException::class);

        (void) $this->account->activate();
    }

    public function testDeleteThrowsWhenAlreadyDeleted(): void
    {
        $deleted = $this->account->delete();

        $this->expectException(AccountInvalidActionException::class);

        (void) $deleted->delete();
    }
}
