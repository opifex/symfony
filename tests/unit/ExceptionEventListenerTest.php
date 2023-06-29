<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Listener\ExceptionEventListener;
use App\Domain\Exception\ValidationFailedHttpException;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

final class ExceptionEventListenerTest extends Unit
{
    private KernelInterface&MockObject $kernel;

    private LoggerInterface&MockObject $logger;

    private NormalizerInterface&MockObject $normalizer;

    private SerializerInterface&MockObject $serializer;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->kernel = $this->createMock(originalClassName: KernelInterface::class);
        $this->logger = $this->createMock(originalClassName: LoggerInterface::class);
        $this->normalizer = $this->createMock(originalClassName: NormalizerInterface::class);
        $this->serializer = $this->createMock(originalClassName: SerializerInterface::class);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testInvokeWithKernelNonDebugMode(): void
    {
        $constraintViolation = new ConstraintViolation(
            message: 'Validation message.',
            messageTemplate: null,
            parameters: [],
            root: null,
            propertyPath: 'property',
            invalidValue: null,
        );
        $normalizedViolation = [
            'validation' => [
                [
                    'name' => $constraintViolation->getPropertyPath(),
                    'reason' => $constraintViolation->getMessage(),
                    'object' => $constraintViolation->getRoot(),
                    'value' => $constraintViolation->getInvalidValue(),
                ],
            ],
        ];

        $this->normalizer
            ->expects($this->once())
            ->method(constraint: 'normalize')
            ->willReturn($normalizedViolation);

        $exceptionEventListener = new ExceptionEventListener(
            logger: $this->logger,
            normalizer: $this->normalizer,
            serializer: $this->serializer,
        );
        $constraintViolationList = new ConstraintViolationList([$constraintViolation]);
        $exceptionEvent = new ExceptionEvent(
            kernel: $this->kernel,
            request: new Request(),
            requestType: HttpKernelInterface::MAIN_REQUEST,
            e: new ValidationFailedHttpException($constraintViolationList),
        );

        ($exceptionEventListener)($exceptionEvent);

        $this->expectNotToPerformAssertions();
    }
}
