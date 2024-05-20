<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Infrastructure\Workflow\AccountMarkingStore;
use Codeception\Test\Unit;
use stdClass;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Marking;

final class AccountMarkingStoreTest extends Unit
{
    public function testGetMarkingThrowsExceptionWithInvalidObject(): void
    {
        $accountMarkingStore = new AccountMarkingStore();

        $this->expectException(InvalidArgumentException::class);

        $accountMarkingStore->getMarking(new stdClass());
    }

    public function testSetMarkingThrowsExceptionWithInvalidObject(): void
    {
        $accountMarkingStore = new AccountMarkingStore();

        $this->expectException(InvalidArgumentException::class);

        $accountMarkingStore->setMarking(new stdClass(), new Marking());
    }
}
