<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Workflow\AccountMarkingStore;
use App\Domain\Contract\AccountRepositoryInterface;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use stdClass;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\Marking;

final class AccountMarkingStoreTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(originalClassName: AccountRepositoryInterface::class);
    }

    public function testGetMarkingThrowsExceptionWithInvalidObject(): void
    {
        $accountMarkingStore = new AccountMarkingStore($this->accountRepository);

        $this->expectException(LogicException::class);

        $accountMarkingStore->getMarking(new stdClass());
    }

    public function testSetMarkingThrowsExceptionWithInvalidObject(): void
    {
        $accountMarkingStore = new AccountMarkingStore($this->accountRepository);

        $this->expectException(LogicException::class);

        $accountMarkingStore->setMarking(new stdClass(), new Marking());
    }
}
