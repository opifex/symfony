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
    /**
     * @throws MockObjectException
     */
    protected function setUp(): void
    {
        $this->kernel = $this->createMock(originalClassName: KernelInterface::class);
        $this->translator = $this->createMock(originalClassName: TranslatorInterface::class);

        $this->violationListNormalizer = new ViolationListNormalizer($this->kernel, $this->translator);
    }

    public function testNormalizeThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->violationListNormalizer->normalize(object: null);
    }
}
