<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Serializer\ViolationListNormalizer;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception as MockObjectException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ViolationListNormalizerTest extends Unit
{
    /**
     * @throws MockObjectException
     */
    #[Override]
    protected function setUp(): void
    {
        $this->kernel = $this->createMock(originalClassName: KernelInterface::class);
        $this->translator = $this->createMock(originalClassName: TranslatorInterface::class);
    }

    public function testNormalizeThrowsInvalidArgumentException(): void
    {
        $violationListNormalizer = new ViolationListNormalizer($this->kernel, $this->translator);

        $this->expectException(InvalidArgumentException::class);

        $violationListNormalizer->normalize(object: null);
    }
}
