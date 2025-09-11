<?php

declare(strict_types=1);

namespace Tests\Functional;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Opis\JsonSchema\Validator;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractWebTestCase extends WebTestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->createClient();
        $this->purgeDatabase();
    }

    public static function loadFixtures(array $fixtures = []): void
    {
        $loader = new Loader();
        array_walk($fixtures, fn(string $fixture) => $loader->addFixture(new $fixture()));
        new ORMExecutor(self::getEntityManager(), new ORMPurger())->execute($loader->getFixtures());
    }

    public static function purgeDatabase(): void
    {
        new ORMExecutor(self::getEntityManager(), new ORMPurger())->getPurger()->purge();
    }

    public static function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(id: 'doctrine')->getManager();
    }

    public static function grabEntityFromRepository(string $entity, array $criteria = []): object
    {
        return self::getEntityManager()->getRepository($entity)->findOneBy($criteria);
    }

    public static function grabArrayFromResponse(): array
    {
        return json_decode(self::getClient()->getResponse()->getContent(), true) ?? [];
    }

    public static function assertResponseBodyIsEmpty(): void
    {
        TestCase::assertEmpty(self::getClient()->getResponse()->getContent());
    }

    public static function assertResponseSchema(string $schemaFile): void
    {
        $schema = json_decode(file_get_contents(__DIR__ . '/../Support/Schema/' . $schemaFile));
        $content = json_decode(self::getClient()->getResponse()->getContent());

        TestCase::assertTrue(new Validator()->validate($content, $schema)->isValid());
    }

    public static function sendAuthorizationRequest(string $email, string $password): void
    {
        self::sendPostRequest(url: '/api/auth/signin', parameters: ['email' => $email, 'password' => $password]);
        self::getClient()->setServerParameter(
            key: 'HTTP_AUTHORIZATION',
            value: 'Bearer ' . self::grabArrayFromResponse()['access_token'] ?? '',
        );
    }

    public static function sendGetRequest(string $url, array $parameters = []): void
    {
        self::getClient()->jsonRequest(method: Request::METHOD_GET, uri: $url, parameters: $parameters);
    }

    public static function sendPostRequest(string $url, array $parameters = []): void
    {
        self::getClient()->jsonRequest(method: Request::METHOD_POST, uri: $url, parameters: $parameters);
    }

    public static function sendDeleteRequest(string $url, array $parameters = []): void
    {
        self::getClient()->jsonRequest(method: Request::METHOD_DELETE, uri: $url, parameters: $parameters);
    }

    public static function sendPatchRequest(string $url, array $parameters = []): void
    {
        self::getClient()->jsonRequest(method: Request::METHOD_PATCH, uri: $url, parameters: $parameters);
    }
}
