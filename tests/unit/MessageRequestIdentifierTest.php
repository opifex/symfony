<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Service\MessageRequestIdentifier;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;

class MessageRequestIdentifierTest extends Unit
{
    public function testIdentifyTwice(): void
    {
        $messageRequestIdentifier = new MessageRequestIdentifier();
        $identifierFirst = $messageRequestIdentifier->identify();

        $this->assertTrue(Uuid::isValid($identifierFirst));

        $identifierSecond = $messageRequestIdentifier->identify();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifierFirst, $identifierSecond);
    }

    public function testIdentifyWithProvidedIdentifier(): void
    {
        $messageRequestIdentifier = new MessageRequestIdentifier();
        $identifier = 'f6f6598a-620b-48cd-a88f-eb72a85cc66d';

        $identifierFirst = $messageRequestIdentifier->identify(identifier: $identifier);

        $this->assertTrue(Uuid::isValid($identifierFirst));
        $this->assertSame($identifier, $identifierFirst);

        $identifierSecond = $messageRequestIdentifier->identify();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifier, $identifierSecond);
    }

    public function testIdentifyWithProvidedRequest(): void
    {
        $messageRequestIdentifier = new MessageRequestIdentifier();
        $request = new Request();
        $identifier = 'f6f6598a-620b-48cd-a88f-eb72a85cc66d';
        $request->headers->set($messageRequestIdentifier->key(), $identifier);

        $identifierFirst = $messageRequestIdentifier->identify($request);

        $this->assertTrue(Uuid::isValid($identifierFirst));
        $this->assertSame($identifier, $identifierFirst);

        $identifierSecond = $messageRequestIdentifier->identify();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifier, $identifierSecond);
    }

    public function testIdentifyWithProvidedRequestAndIdentifier(): void
    {
        $messageRequestIdentifier = new MessageRequestIdentifier();
        $request = new Request();
        $identifierRequest = 'f6f6598a-620b-48cd-a88f-eb72a85cc66d';
        $identifierProvided = '1a39e356-09c5-4232-9e75-8243e41700b1';
        $request->headers->set($messageRequestIdentifier->key(), $identifierRequest);

        $identifierFirst = $messageRequestIdentifier->identify($request, $identifierProvided);

        $this->assertTrue(Uuid::isValid($identifierFirst));
        $this->assertSame($identifierProvided, $identifierFirst);

        $identifierSecond = $messageRequestIdentifier->identify();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifierProvided, $identifierSecond);
    }

    public function testIdentifyWithUpdatedIdentifier(): void
    {
        $messageRequestIdentifier = new MessageRequestIdentifier();
        $identifier = 'f6f6598a-620b-48cd-a88f-eb72a85cc66d';
        $identifierUpdated = '1a39e356-09c5-4232-9e75-8243e41700b1';

        $identifierFirst = $messageRequestIdentifier->identify(identifier: $identifier);

        $this->assertTrue(Uuid::isValid($identifierFirst));
        $this->assertSame($identifier, $identifierFirst);

        $identifierSecond = $messageRequestIdentifier->identify(identifier: $identifierUpdated);

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifierUpdated, $identifierSecond);

        $identifierThird = $messageRequestIdentifier->identify();

        $this->assertSame($identifierUpdated, $identifierThird);
    }
}
