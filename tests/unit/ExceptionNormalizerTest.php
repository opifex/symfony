<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Serializer\ExceptionNormalizer;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpKernel\KernelInterface;

final class ExceptionNormalizerTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->kernel = $this->createMock(originalClassName: KernelInterface::class);
    }

    public function testNormalizeThrowsInvalidArgumentException(): void
    {
        $exceptionNormalizer = new ExceptionNormalizer($this->kernel);
        $normalized = $exceptionNormalizer->normalize(object: null);

        $this->assertArrayHasKey(key: 'error', array: $normalized);
        $this->assertEquals(expected: 'Object expected to be a valid exception type.', actual: $normalized['error']);
    }
}
