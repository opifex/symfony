<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Domain\Account\AccountIdentifier;
use DomainException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class AbstractUuidIdentifierTest extends TestCase
{
    #[DataProvider(methodName: 'validUuidProvider')]
    public function testEnsureValidUuidIsAccepted(string $uuid): void
    {
        $identifier = AccountIdentifier::fromString(uuid: $uuid);

        self::assertSame($uuid, $identifier->toString());
    }

    #[DataProvider(methodName: 'invalidUuidProvider')]
    public function testTryToCreateIdentifierWithInvalidUuid(string $uuid): void
    {
        $this->expectException(DomainException::class);
        $this->expectExceptionMessage(message: 'Invalid UUID identifier provided.');

        AccountIdentifier::fromString(uuid: $uuid);
    }

    public static function validUuidProvider(): iterable
    {
        yield 'uuid v1' => ['uuid' => 'c232ab00-9414-11ec-b3c8-9e6bdeced846'];
        yield 'uuid v4' => ['uuid' => '550e8400-e29b-41d4-a716-446655440000'];
        yield 'uuid v7' => ['uuid' => '018f8b8c-1b8a-7d8a-8e8e-1f8e8e8e8e8e'];
        yield 'uppercase uuid' => ['uuid' => '550E8400-E29B-41D4-A716-446655440000'];
    }

    public static function invalidUuidProvider(): iterable
    {
        yield 'empty string' => ['uuid' => ''];
        yield 'not a uuid' => ['uuid' => 'not-a-uuid'];
        yield 'nil uuid' => ['uuid' => '00000000-0000-0000-0000-000000000000'];
        yield 'invalid version' => ['uuid' => '550e8400-e29b-91d4-a716-446655440000'];
        yield 'invalid variant' => ['uuid' => '550e8400-e29b-41d4-c716-446655440000'];
        yield 'missing hyphens' => ['uuid' => '550e8400e29b41d4a716446655440000'];
        yield 'extra characters' => ['uuid' => '550e8400-e29b-41d4-a716-446655440000-extra'];
        yield 'invalid hex' => ['uuid' => '550e8400-e29b-41d4-a716-44665544000z'];
    }
}
