<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Serializer\RequestNormalizer;
use Override;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;

#[AllowDynamicProperties]
final class RequestNormalizerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->uploadedFile = $this->createMock(type: UploadedFile::class);
    }

    #[DataProvider(methodName: 'queryDataProvider')]
    public function testNormalizeQueryWithDifferentData(mixed $value, mixed $expected): void
    {
        $requestNormalizer = new RequestNormalizer();
        $request = new Request(query: ['value' => $value]);
        $normalized = $requestNormalizer->normalize($request);

        $this->assertArrayHasKey(key: 'value', array: $normalized);
        $this->assertSame($expected, $normalized['value']);
    }

    #[DataProvider(methodName: 'contentDataProvider')]
    public function testNormalizeContentWithDifferentData(mixed $value, mixed $expected): void
    {
        $requestNormalizer = new RequestNormalizer();
        $request = new Request(content: json_encode(['value' => $value]));
        $normalized = $requestNormalizer->normalize($request);

        $this->assertArrayHasKey(key: 'value', array: $normalized);
        $this->assertSame($expected, $normalized['value']);
    }

    public function testNormalizeUploadedFile(): void
    {
        $this->uploadedFile
            ->expects($this->once())
            ->method(constraint: 'getClientOriginalName')
            ->willReturn(value: 'test.txt');

        $this->uploadedFile
            ->expects($this->once())
            ->method(constraint: 'getContent')
            ->willReturn(value: 'content');

        $this->uploadedFile
            ->expects($this->once())
            ->method(constraint: 'getMimeType')
            ->willReturn(value: 'text/plain');

        $this->uploadedFile
            ->expects($this->atLeastOnce())
            ->method(constraint: 'getSize')
            ->willReturn(value: 7);

        $requestNormalizer = new RequestNormalizer();
        $request = new Request(files: ['file' => $this->uploadedFile]);
        $normalized = $requestNormalizer->normalize($request);

        $this->assertSame(expected: 'test.txt', actual: $normalized['file']['filename']);
        $this->assertSame(expected: 'text/plain', actual: $normalized['file']['mime_type']);
        $this->assertSame(expected: 'content', actual: $normalized['file']['content']);
        $this->assertSame(expected: 7, actual: $normalized['file']['size']);
    }

    public function testNormalizeUploadedFilesArray(): void
    {
        $this->uploadedFile
            ->expects($this->once())
            ->method(constraint: 'getClientOriginalName')
            ->willReturn(value: 'test.txt');

        $this->uploadedFile
            ->expects($this->once())
            ->method(constraint: 'getContent')
            ->willReturn(value: 'content');

        $this->uploadedFile
            ->expects($this->once())
            ->method(constraint: 'getMimeType')
            ->willReturn(value: 'text/plain');

        $this->uploadedFile
            ->expects($this->atLeastOnce())
            ->method(constraint: 'getSize')
            ->willReturn(value: 7);

        $requestNormalizer = new RequestNormalizer();
        $request = new Request(files: ['file' => [$this->uploadedFile]]);
        $normalized = $requestNormalizer->normalize($request);

        $this->assertSame(expected: 'test.txt', actual: $normalized['file'][0]['filename']);
        $this->assertSame(expected: 'text/plain', actual: $normalized['file'][0]['mime_type']);
        $this->assertSame(expected: 'content', actual: $normalized['file'][0]['content']);
        $this->assertSame(expected: 7, actual: $normalized['file'][0]['size']);
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

    public static function queryDataProvider(): iterable
    {
        yield 'string remains string' => ['value' => 'string', 'expected' => 'string'];
        yield 'string number remains integer' => ['value' => '100', 'expected' => 100];
        yield 'array remains array' => ['value' => ['array' => 100], 'expected' => ['array' => 100]];
        yield 'integer remains integer' => ['value' => 100, 'expected' => 100];
        yield 'float remains float' => ['value' => 100.56, 'expected' => 100.56];
        yield 'null remains null' => ['value' => null, 'expected' => null];
        yield 'string "null" to null' => ['value' => 'null', 'expected' => null];
        yield 'boolean true remains true' => ['value' => true, 'expected' => true];
        yield 'string "true" to boolean true' => ['value' => 'true', 'expected' => true];
        yield 'boolean false remains false' => ['value' => false, 'expected' => false];
        yield 'string "false" to boolean false' => ['value' => 'false', 'expected' => false];
        yield 'scientific number remains string' => ['value' => '38328e88', 'expected' => '38328e88'];
    }

    public static function contentDataProvider(): iterable
    {
        yield 'string remains string' => ['value' => 'string', 'expected' => 'string'];
        yield 'string number remains string' => ['value' => '100', 'expected' => '100'];
        yield 'array remains array' => ['value' => ['array' => 100], 'expected' => ['array' => 100]];
        yield 'integer remains integer' => ['value' => 100, 'expected' => 100];
        yield 'float remains float' => ['value' => 100.56, 'expected' => 100.56];
        yield 'null remains null' => ['value' => null, 'expected' => null];
        yield 'boolean true remains true' => ['value' => true, 'expected' => true];
        yield 'boolean false remains false' => ['value' => false, 'expected' => false];
        yield 'scientific number remains string' => ['value' => '38328e88', 'expected' => '38328e88'];
    }
}
