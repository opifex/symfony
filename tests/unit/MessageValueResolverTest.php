<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Attribute\MapMessage;
use App\Application\Service\MessageValueResolver;
use App\Domain\Exception\NormalizationFailedException;
use Codeception\Test\Unit;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class MessageValueResolverTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->denormalizer = $this->createMock(originalClassName: DenormalizerInterface::class);
        $this->normalizer = $this->createMock(originalClassName: NormalizerInterface::class);
        $this->validator = $this->createMock(originalClassName: ValidatorInterface::class);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testResolveThrowsExceptionOnNormalizationFailed(): void
    {
        $messageValueResolver = new MessageValueResolver($this->denormalizer, $this->normalizer, $this->validator);

        $this->normalizer
            ->expects($this->once())
            ->method(constraint: 'normalize')
            ->willThrowException(new LogicException());

        $this->expectException(NormalizationFailedException::class);

        $messageValueResolver->resolve(new Request(content: 'invalid'), new ArgumentMetadata(
            name: 'message',
            type: null,
            isVariadic: false,
            hasDefaultValue: false,
            defaultValue: null,
            attributes: [new MapMessage(resolver: MessageValueResolver::class)],
        ));
    }
}
