<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Serializer\ExceptionNormalizer;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ExceptionNormalizerTest extends Unit
{
    private KernelInterface&MockObject $kernel;
    private TranslatorInterface&MockObject $translator;

    /**
     * @throws MockObjectException
     */
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
}
