<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Serializer\ViolationListNormalizer;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ViolationListNormalizerTest extends Unit
{
    private ViolationListNormalizer $violationListNormalizer;

    /**
     * @throws MockObjectException
     */
    protected function setUp(): void
    {
        $kernel = $this->createMock(originalClassName: KernelInterface::class);
        $translator = $this->createMock(originalClassName: TranslatorInterface::class);
        $this->violationListNormalizer = new ViolationListNormalizer($kernel, $translator);
    }

    public function testNormalizeThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->violationListNormalizer->normalize(object: null);
    }
}
