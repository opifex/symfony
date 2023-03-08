<?php

declare(strict_types=1);

namespace App\Tests;

use App\Infrastructure\Persistence\Listener\PrePersistListener;
use Codeception\Test\Unit;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PrePersistListenerTest extends Unit
{
    private EntityManagerInterface&MockObject $entityManager;

    private PrePersistListener $prePersistListener;

    private ValidatorInterface&MockObject $validator;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(originalClassName: EntityManagerInterface::class);
        $this->validator = $this->createMock(originalClassName: ValidatorInterface::class);
        $this->prePersistListener = new PrePersistListener($this->validator);
    }

    /**
     * @throws Exception
     */
    public function testInvokeDoesNotThrowExceptionOnSuccessfulValidation(): void
    {
        $constraints = $this->createMock(originalClassName: ConstraintViolationListInterface::class);
        $constraints
            ->expects($this->once())
            ->method(constraint: 'count')
            ->willReturn(value: 0);

        $this->validator
            ->expects($this->once())
            ->method(constraint: 'validate')
            ->with(new stdClass())
            ->willReturn($constraints);

        ($this->prePersistListener)(new PrePersistEventArgs(new stdClass(), $this->entityManager));
    }

    public function testInvokeThrowsExceptionOnValidationFailed(): void
    {
        $message = '';
        $constraints = new ConstraintViolationList(
            [new ConstraintViolation($message, $message, [], $message, $message, $message)],
        );

        $this->validator
            ->expects($this->once())
            ->method(constraint: 'validate')
            ->willReturn($constraints);

        $this->expectException(ValidationFailedException::class);

        ($this->prePersistListener)(new PrePersistEventArgs(new stdClass(), $this->entityManager));

        $this->expectNotToPerformAssertions();
    }
}
