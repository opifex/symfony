<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Contract\PrivacyProtectorInterface;
use App\Infrastructure\Logging\RequestProcessor;
use Codeception\Test\Unit;
use DateTimeImmutable;
use Monolog\Level;
use Monolog\LogRecord;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RequestProcessorTest extends Unit
{
    /**
     * @throws Exception
     */
    #[Override]
    protected function setUp(): void
    {
        $this->normalizer = $this->createMock(originalClassName: NormalizerInterface::class);
        $this->privacyProtector = $this->createMock(originalClassName: PrivacyProtectorInterface::class);
        $this->requestStack = $this->createMock(originalClassName: RequestStack::class);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testInvokeNotThrowsExceptionOnNormalizationFailed(): void
    {
        $requestProcessor = new RequestProcessor($this->normalizer, $this->privacyProtector, $this->requestStack);

        $this->normalizer
            ->expects($this->once())
            ->method(constraint: 'normalize')
            ->willThrowException(new LogicException());

        $logRecord = new LogRecord(new DateTimeImmutable(), channel: 'test', level: Level::Debug, message: '');

        ($requestProcessor)($logRecord);
    }
}
