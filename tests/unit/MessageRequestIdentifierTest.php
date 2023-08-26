<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Service\MessageRequestIdentifier;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class MessageRequestIdentifierTest extends Unit
{
    protected function setUp(): void
    {
        $this->messageRequestIdentifier = new MessageRequestIdentifier();
    }

    public function testIdentifyTwice(): void
    {
        $identifierFirst = $this->messageRequestIdentifier->identify();

        $this->assertTrue(Uuid::isValid($identifierFirst));

        $identifierSecond = $this->messageRequestIdentifier->identify();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifierFirst, $identifierSecond);
    }

    public function testIdentifyWithProvidedIdentifier(): void
    {
        $identifier = 'f6f6598a-620b-48cd-a88f-eb72a85cc66d';

        $identifierFirst = $this->messageRequestIdentifier->identify(identifier: $identifier);

        $this->assertTrue(Uuid::isValid($identifierFirst));
        $this->assertSame($identifier, $identifierFirst);

        $identifierSecond = $this->messageRequestIdentifier->identify();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifier, $identifierSecond);
    }

    public function testIdentifyWithProvidedRequest(): void
    {
        $request = new Request();
        $identifier = 'f6f6598a-620b-48cd-a88f-eb72a85cc66d';
        $request->headers->set($this->messageRequestIdentifier->key(), $identifier);

        $identifierFirst = $this->messageRequestIdentifier->identify($request);

        $this->assertTrue(Uuid::isValid($identifierFirst));
        $this->assertSame($identifier, $identifierFirst);

        $identifierSecond = $this->messageRequestIdentifier->identify();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifier, $identifierSecond);
    }

    public function testIdentifyWithProvidedRequestAndIdentifier(): void
    {
        $request = new Request();
        $identifierRequest = 'f6f6598a-620b-48cd-a88f-eb72a85cc66d';
        $identifierProvided = '1a39e356-09c5-4232-9e75-8243e41700b1';
        $request->headers->set($this->messageRequestIdentifier->key(), $identifierRequest);

        $identifierFirst = $this->messageRequestIdentifier->identify($request, $identifierProvided);

        $this->assertTrue(Uuid::isValid($identifierFirst));
        $this->assertSame($identifierProvided, $identifierFirst);

        $identifierSecond = $this->messageRequestIdentifier->identify();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifierProvided, $identifierSecond);
    }

    public function testIdentifyWithUpdatedIdentifier(): void
    {
        $identifier = 'f6f6598a-620b-48cd-a88f-eb72a85cc66d';
        $identifierUpdated = '1a39e356-09c5-4232-9e75-8243e41700b1';

        $identifierFirst = $this->messageRequestIdentifier->identify(identifier: $identifier);

        $this->assertTrue(Uuid::isValid($identifierFirst));
        $this->assertSame($identifier, $identifierFirst);

        $identifierSecond = $this->messageRequestIdentifier->identify(identifier: $identifierUpdated);

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifierUpdated, $identifierSecond);

        $identifierThird = $this->messageRequestIdentifier->identify();

        $this->assertSame($identifierUpdated, $identifierThird);
    }
}
