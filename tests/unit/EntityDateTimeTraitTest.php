<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Entity\Account;
use Codeception\Test\Unit;
use DateTimeImmutable;

final class EntityDateTimeTraitTest extends Unit
{
    private Account $account;

    protected function setUp(): void
    {
        $this->account = new Account(email: 'email@example.com');
    }

    public function testPrePersistDateTime(): void
    {
        $this->account->prePersistDateTime();

        $this->assertInstanceOf(expected: DateTimeImmutable::class, actual: $this->account->getCreatedAt());
        $this->assertInstanceOf(expected: DateTimeImmutable::class, actual: $this->account->getUpdatedAt());
    }

    public function testPreUpdateDateTime(): void
    {
        $this->account->preUpdateDateTime();

        $this->assertNull($this->account->getCreatedAt());
        $this->assertInstanceOf(expected: DateTimeImmutable::class, actual: $this->account->getUpdatedAt());
    }
}
