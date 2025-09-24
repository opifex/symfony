<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\Serializer\RequestNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

final class RequestNormalizerTest extends TestCase
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

    public static function requestDataProvider(): iterable
    {
        yield 'string remains string' => ['value' => 'string', 'expected' => 'string'];
        yield 'array with string number to int' => ['value' => ['array' => '100'], 'expected' => ['array' => 100]];
        yield 'integer remains integer' => ['value' => 100, 'expected' => 100];
        yield 'string number to integer' => ['value' => '100', 'expected' => 100];
        yield 'float remains float' => ['value' => 100.56, 'expected' => 100.56];
        yield 'string float to float' => ['value' => '100.56', 'expected' => 100.56];
        yield 'null remains null' => ['value' => null, 'expected' => null];
        yield 'string "null" to null' => ['value' => 'null', 'expected' => null];
        yield 'boolean true remains true' => ['value' => true, 'expected' => true];
        yield 'string "true" to boolean true' => ['value' => 'true', 'expected' => true];
        yield 'boolean false remains false' => ['value' => false, 'expected' => false];
        yield 'string "false" to boolean false' => ['value' => 'false', 'expected' => false];
    }
}
