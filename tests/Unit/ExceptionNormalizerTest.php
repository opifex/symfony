<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Serializer\ExceptionNormalizer;
use Exception;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ExceptionNormalizerTest extends TestCase
{
    private KernelInterface&MockObject $kernel;

    private TranslatorInterface&MockObject $translator;

    #[Override]
    protected function setUp(): void
    {
        $this->kernel = $this->createMock(type: KernelInterface::class);
        $this->translator = $this->createMock(type: TranslatorInterface::class);
    }

    public function testNormalizeThrowsInvalidArgumentException(): void
    {
        $exceptionNormalizer = new ExceptionNormalizer($this->kernel, $this->translator);

        $this->translator
            ->expects($this->once())
            ->method(constraint: 'trans')
            ->with(arguments: 'Object expected to be a valid exception type.')
            ->willReturn(value: 'Object expected to be a valid exception type.');

        $normalized = $exceptionNormalizer->normalize(data: null);

        $this->assertArrayHasKey(key: 'error', array: $normalized);
        $this->assertEquals(expected: 'Object expected to be a valid exception type.', actual: $normalized['error']);
    }

    public function testNormalizeDebugModeWithViolations(): void
    {
        $exceptionNormalizer = new ExceptionNormalizer($this->kernel, $this->translator);

        $this->kernel
            ->expects($this->atLeastOnce())
            ->method(constraint: 'isDebug')
            ->willReturn(value: true);

        $throwable = new class extends Exception {
            public function getViolations(): ConstraintViolationListInterface
            {
                return new ConstraintViolationList([
                    new ConstraintViolation(
                        message: 'This field was not expected.',
                        messageTemplate: null,
                        parameters: [],
                        root: new stdClass(),
                        propertyPath: 'property',
                        invalidValue: null,
                    ),
                    new ConstraintViolation(
                        message: 'This field was not expected.',
                        messageTemplate: null,
                        parameters: [],
                        root: 'root',
                        propertyPath: 'property',
                        invalidValue: null,
                    ),
                ]);
            }
        };

        $normalized = $exceptionNormalizer->normalize($throwable);

        $this->assertArrayHasKey(key: 'code', array: $normalized);
        $this->assertArrayHasKey(key: 'error', array: $normalized);
        $this->assertArrayHasKey(key: 'violations', array: $normalized);
        $this->assertArrayHasKey(key: 'trace', array: $normalized);
    }
}
