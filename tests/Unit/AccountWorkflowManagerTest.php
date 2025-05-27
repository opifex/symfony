<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Service\AccountWorkflowManager;
use App\Domain\Contract\Account\AccountEntityRepositoryInterface;
use App\Domain\Exception\Account\AccountNotFoundException;
use App\Domain\Model\AccountIdentifier;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Workflow\WorkflowInterface;

final class AccountWorkflowManagerTest extends Unit
{
    private AccountEntityRepositoryInterface&MockObject $accountEntityRepository;

    private WorkflowInterface&MockObject $workflow;

    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->accountEntityRepository = $this->createMock(type: AccountEntityRepositoryInterface::class);
        $this->workflow = $this->createMock(type: WorkflowInterface::class);
    }

    public function testActivateThrowsExceptionWithInvalidAccountIdentifier(): void
    {
        $AccountWorkflowManager = new AccountWorkflowManager(
            accountEntityRepository: $this->accountEntityRepository,
            accountStateMachine: $this->workflow,
        );

        $this->accountEntityRepository
            ->expects($this->once())
            ->method(constraint: 'findOneByid')
            ->willReturn(value: null);

        $this->expectException(exception: AccountNotFoundException::class);

        $AccountWorkflowManager->activate(new AccountIdentifier(uuid: '00000000-0000-6000-8000-000000000000'));
    }
}
