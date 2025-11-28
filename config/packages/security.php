<?php

declare(strict_types=1);

use App\Domain\Account\AccountRole;
use App\Infrastructure\Security\AccessTokenHandler\JwtAccessTokenHandler;
use App\Infrastructure\Security\AuthenticatedUser\PasswordAuthenticatedUserChecker;
use App\Infrastructure\Security\DatabaseUserProvider;
use App\Infrastructure\Security\LoginAuthenticator\JsonLoginAuthenticator;
use Symfony\Component\DependencyInjection\Loader\Configurator\App;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return App::config([
    'security' => [
        'providers' => [
            [
                'name' => 'database',
                'id' => DatabaseUserProvider::class,
            ],
        ],
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => [
                'algorithm' => 'auto',
            ],
        ],
        'firewalls' => [
            [
                'name' => 'development',
                'pattern' => '^/(_(profiler|wdt))',
                'security' => false,
            ],
            [
                'name' => 'authentication',
                'stateless' => true,
                'pattern' => '^/api/auth/signin',
                'provider' => 'database',
                'user_checker' => PasswordAuthenticatedUserChecker::class,
                'custom_authenticators' => [
                    JsonLoginAuthenticator::class,
                ],
            ],
            [
                'name' => 'authorization',
                'stateless' => true,
                'pattern' => '^/api',
                'access_token' => [
                    'token_handler' => JwtAccessTokenHandler::class,
                ],
            ],
        ],
        'role_hierarchy' => [
            AccountRole::Admin->value => [
                AccountRole::User->value,
            ],
        ],
    ],
    'when@test' => [
        'security' => [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'plaintext',
                ],
            ],
        ],
    ],
]);
