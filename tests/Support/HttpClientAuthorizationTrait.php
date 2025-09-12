<?php

declare(strict_types=1);

namespace Tests\Support;

use Symfony\Component\HttpFoundation\Request;

trait HttpClientAuthorizationTrait
{
    public static function sendAuthorizationRequest(string $email, string $password): void
    {
        self::getClient()->jsonRequest(
            method: Request::METHOD_POST,
            uri: '/api/auth/signin',
            parameters: ['email' => $email, 'password' => $password],
            changeHistory: false,
        );
        self::getClient()->setServerParameter(
            key: 'HTTP_AUTHORIZATION',
            value: 'Bearer ' . json_decode(self::getClient()->getResponse()->getContent(), true)['access_token'] ?? '',
        );
    }
}
