<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Contract\AccountRepositoryInterface;
use App\Infrastructure\Workflow\AccountMarkingStore;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Marking;

final class AccountMarkingStoreTest extends Unit
{
    private AccountRepositoryInterface&MockObject $accountRepository;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(type: AccountRepositoryInterface::class);
    }

    public function testGetMarkingThrowsExceptionWithInvalidObject(): void
    {
        $accountMarkingStore = new AccountMarkingStore($this->accountRepository);

        $this->expectException(InvalidArgumentException::class);

        $accountMarkingStore->getMarking(new stdClass());
    }

    public function testSetMarkingThrowsExceptionWithInvalidObject(): void
    {
        $accountMarkingStore = new AccountMarkingStore($this->accountRepository);

        $this->expectException(InvalidArgumentException::class);

        $accountMarkingStore->setMarking(new stdClass(), new Marking());
    }
}
