<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Serializer\ExceptionNormalizer;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Validator\ConstraintViolationList;

final class ExceptionNormalizerTest extends Unit
{
    /**
     * @throws MockObjectException
     */
    protected function setUp(): void
    {
        $kernel = $this->createMock(originalClassName: KernelInterface::class);
        $this->exceptionNormalizer = new ExceptionNormalizer($kernel);
        $this->violations = new ConstraintViolationList();
    }

    public function testNormalizeThrowsInvalidArgumentException(): void
    {
        $normalized = $this->exceptionNormalizer->normalize(object: null);

        $this->assertArrayHasKey(key: 'error', array: $normalized);
        $this->assertEquals(expected: 'Object expected to be a valid exception type.', actual: $normalized['error']);
    }
}
