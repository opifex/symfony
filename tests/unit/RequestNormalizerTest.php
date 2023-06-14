<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Serializer\RequestNormalizer;
use Codeception\Test\Unit;
use stdClass;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

final class RequestNormalizerTest extends Unit
{
    private RequestNormalizer $requestNormalizer;

    protected function setUp(): void
    {
        $this->requestNormalizer = new RequestNormalizer();
    }

    public function testGetSupportedTypes(): void
    {
        $supportedTypes = $this->requestNormalizer->getSupportedTypes(format: null);

        $this->assertEquals(expected: [Request::class => true], actual: $supportedTypes);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeThrowsInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->requestNormalizer->normalize(new stdClass());
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeWithDifferentTypes(): void
    {
        $data = [
            'string' => 'normalize',
            'array' => ['integer_string' => '100'],

            'integer' => 100,
            'integer_string' => '100',

            'float' => 100.56,
            'float_string' => '100.56',

            'nullable' => null,
            'nullable_string' => 'null',

            'boolean_true' => true,
            'boolean_true_string' => 'true',

            'boolean_false' => false,
            'boolean_false_string' => 'false',
        ];

        $request = new Request(content: json_encode($data));
        $normalized = $this->requestNormalizer->normalize($request);

        $identifier = 'string';
        $this->assertIsString($normalized[$identifier]);
        $this->assertEquals($data[$identifier], $normalized[$identifier]);

        $identifier = 'array';
        $this->assertIsArray($normalized[$identifier]);
        $this->assertIsInt($normalized[$identifier]['integer_string']);
        $this->assertEquals($data[$identifier], $normalized[$identifier]);

        $identifier = 'integer';
        $this->assertIsInt($normalized[$identifier]);
        $this->assertEquals($data[$identifier], $normalized[$identifier]);

        $identifier = 'integer_string';
        $this->assertIsInt($normalized[$identifier]);
        $this->assertEquals($data['integer'], $normalized[$identifier]);

        $identifier = 'float';
        $this->assertIsFloat($normalized[$identifier]);
        $this->assertEquals($data[$identifier], $normalized[$identifier]);

        $identifier = 'float_string';
        $this->assertIsFloat($normalized[$identifier]);
        $this->assertEquals($data['float'], $normalized[$identifier]);

        $identifier = 'nullable';
        $this->assertNull($normalized[$identifier]);
        $this->assertEquals($data[$identifier], $normalized[$identifier]);

        $identifier = 'nullable_string';
        $this->assertNull($normalized[$identifier]);
        $this->assertEquals($data['nullable'], $normalized[$identifier]);

        $identifier = 'boolean_true';
        $this->assertIsBool($normalized[$identifier]);
        $this->assertEquals($data[$identifier], $normalized[$identifier]);

        $identifier = 'boolean_true_string';
        $this->assertIsBool($normalized[$identifier]);
        $this->assertEquals($data['boolean_true'], $normalized[$identifier]);

        $identifier = 'boolean_false';
        $this->assertIsBool($normalized[$identifier]);
        $this->assertEquals($data[$identifier], $normalized[$identifier]);

        $identifier = 'boolean_false_string';
        $this->assertIsBool($normalized[$identifier]);
        $this->assertEquals($data['boolean_false'], $normalized[$identifier]);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testNormalizeWithInvalidRequest(): void
    {
        $this->expectException(JsonException::class);

        $this->requestNormalizer->normalize(new Request(content: 'invalid'));
    }

    public function testSupportsNormalization(): void
    {
        $this->assertTrue($this->requestNormalizer->supportsNormalization(new Request()));
        $this->assertFalse($this->requestNormalizer->supportsNormalization(new stdClass()));
    }
}
