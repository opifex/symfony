<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Service\MessageRequestIdentifier;
use Codeception\Test\Unit;
use Symfony\Component\Uid\Uuid;

final class MessageRequestIdentifierTest extends Unit
{
    public function testGenerateIdentifierTwice(): void
    {
        $messageRequestIdentifier = new MessageRequestIdentifier();
        $identifierFirst = $messageRequestIdentifier->getIdentifier();

        $this->assertTrue(Uuid::isValid($identifierFirst));

        $identifierSecond = $messageRequestIdentifier->getIdentifier();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifierFirst, $identifierSecond);
    }

    public function testIdentifyWithProvidedIdentifier(): void
    {
        $messageRequestIdentifier = new MessageRequestIdentifier();
        $identifier = 'f6f6598a-620b-48cd-a88f-eb72a85cc66d';

        $messageRequestIdentifier->setIdentifier($identifier);
        $identifierFirst = $messageRequestIdentifier->getIdentifier();

        $this->assertTrue(Uuid::isValid($identifierFirst));
        $this->assertSame($identifier, $identifierFirst);

        $identifierSecond = $messageRequestIdentifier->getIdentifier();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifier, $identifierSecond);
    }

    public function testIdentifyWithUpdatedIdentifier(): void
    {
        $messageRequestIdentifier = new MessageRequestIdentifier();
        $identifier = 'f6f6598a-620b-48cd-a88f-eb72a85cc66d';
        $identifierUpdated = '1a39e356-09c5-4232-9e75-8243e41700b1';

        $messageRequestIdentifier->setIdentifier($identifier);
        $identifierFirst = $messageRequestIdentifier->getIdentifier();

        $this->assertTrue(Uuid::isValid($identifierFirst));
        $this->assertSame($identifier, $identifierFirst);

        $messageRequestIdentifier->setIdentifier($identifierUpdated);
        $identifierSecond = $messageRequestIdentifier->getIdentifier();

        $this->assertTrue(Uuid::isValid($identifierSecond));
        $this->assertSame($identifierUpdated, $identifierSecond);

        $identifierThird = $messageRequestIdentifier->getIdentifier();

        $this->assertSame($identifierUpdated, $identifierThird);
    }
}
