<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Service\CoreAnalyzer;
use App\Domain\Contract\Adapter\ApiClientAdapterInterface;
use Codeception\Test\Unit;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

final class CoreAnalyzerTest extends Unit
{
    private CoreAnalyzer $coreAnalyzer;

    private ApiClientAdapterInterface&MockObject $apiClientAdapter;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->apiClientAdapter = $this->createMock(originalClassName: ApiClientAdapterInterface::class);
        $this->coreAnalyzer = new CoreAnalyzer(new ArrayAdapter(), $this->apiClientAdapter);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testCache(): void
    {
        $this->assertEquals(expected: 'value', actual: $this->coreAnalyzer->cache('key', 'value'));
    }

    public function testHttpbin(): void
    {
        $httpbinResponse = [
            'slideshow' => [
                'author' => 'Yours Truly',
                'title' => 'Sample Slide Show',
            ],
        ];
        $this->apiClientAdapter
            ->expects($this->once())
            ->method(constraint: 'getJson')
            ->willReturn($httpbinResponse);

        $this->assertEquals($httpbinResponse, $this->coreAnalyzer->httpbin());
    }
}
