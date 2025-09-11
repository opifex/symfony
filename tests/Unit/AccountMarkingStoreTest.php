<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Workflow\AccountMarkingStore;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Marking;

final class AccountMarkingStoreTest extends TestCase
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
