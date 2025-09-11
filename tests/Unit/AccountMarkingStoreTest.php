<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Infrastructure\Workflow\AccountMarkingStore;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
use Symfony\Component\Workflow\Marking;

final class AccountMarkingStoreTest extends TestCase
{
    private AccountEntityRepositoryInterface&MockObject $accountEntityRepository;

    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
    }

    public function testGetMarkingThrowsExceptionWithInvalidObject(): void
    {
        $accountMarkingStore = new AccountMarkingStore($this->accountEntityRepository);

        $this->expectException(InvalidArgumentException::class);

        $accountMarkingStore->getMarking(new stdClass());
    }

    public function testSetMarkingThrowsExceptionWithInvalidObject(): void
    {
        $accountMarkingStore = new AccountMarkingStore($this->accountEntityRepository);

        $this->expectException(InvalidArgumentException::class);

        $accountMarkingStore->setMarking(new stdClass(), new Marking());
    }
}
