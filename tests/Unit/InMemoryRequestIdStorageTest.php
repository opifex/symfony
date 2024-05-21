<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Service\InMemoryRequestIdStorage;
use Codeception\Test\Unit;

final class InMemoryRequestIdStorageTest extends Unit
{
    public function testInMemoryStorage(): void
    {
        $inMemoryRequestIdStorage = new InMemoryRequestIdStorage();

        $requestId = '00000000-0000-6000-8000-000000000000';
        $inMemoryRequestIdStorage->setRequestId($requestId);

        $this->assertEquals($requestId, $inMemoryRequestIdStorage->getRequestId());

        $inMemoryRequestIdStorage->reset();

        $this->assertEmpty($inMemoryRequestIdStorage->getRequestId());
    }
}
