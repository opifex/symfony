<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Serializer\RequestNormalizer;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

final class RequestNormalizerTest extends Unit
{
    #[DataProvider(methodName: 'requestDataProvider')]
    public function testNormalizeWithDifferentTypes(mixed $value, mixed $expected): void
    {
        $requestNormalizer = new RequestNormalizer();
        $request = new Request(content: json_encode(['value' => $value]));
        $normalized = $requestNormalizer->normalize($request);

        $this->assertArrayHasKey(key: 'value', array: $normalized);
        $this->assertSame($expected, $normalized['value']);
    }

    public function testGetSupportedTypes(): void
    {
        $requestNormalizer = new RequestNormalizer();
        $supportedTypes = $requestNormalizer->getSupportedTypes(format: null);

        $this->assertArrayHasKey(key: Request::class, array: $supportedTypes);
        $this->assertTrue($supportedTypes[Request::class]);
    }

    public function testCheckSupportsNormalization(): void
    {
        $requestNormalizer = new RequestNormalizer();

        $this->assertTrue($requestNormalizer->supportsNormalization(new Request()));
        $this->assertFalse($requestNormalizer->supportsNormalization(new stdClass()));
    }

    public function testNormalizeThrowsInvalidArgumentException(): void
    {
        $requestNormalizer = new RequestNormalizer();

        $this->expectException(InvalidArgumentException::class);

        $requestNormalizer->normalize(new stdClass());
    }

    public function testNormalizeWithInvalidRequest(): void
    {
        $requestNormalizer = new RequestNormalizer();
        $normalized = $requestNormalizer->normalize(new Request(content: 'invalid'));

        $this->assertEquals(expected: [], actual: $normalized);
    }

    protected function requestDataProvider(): array
    {
        return [
            ['value' => 'string', 'expected' => 'string'],
            ['value' => ['array' => '100'], 'expected' => ['array' => 100]],

            ['value' => 100, 'expected' => 100],
            ['value' => '100', 'expected' => 100],

            ['value' => 100.56, 'expected' => 100.56],
            ['value' => '100.56', 'expected' => 100.56],

            ['value' => null, 'expected' => null],
            ['value' => 'null', 'expected' => null],

            ['value' => true, 'expected' => true],
            ['value' => 'true', 'expected' => true],

            ['value' => false, 'expected' => false],
            ['value' => 'false', 'expected' => false],
        ];
    }
}
