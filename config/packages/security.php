<?php

declare(strict_types=1);

use App\Domain\Model\AccountRole;
use App\Infrastructure\Security\DatabaseUserProvider;
use App\Infrastructure\Security\JsonLoginAuthenticator;
use App\Infrastructure\Security\JwtAccessTokenHandler;
use App\Infrastructure\Security\PasswordAuthenticatedUserChecker;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return function (ContainerConfigurator $configurator): void {
    $configurator->extension(namespace: 'security', config: [
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
        ],
        'providers' => [
            'database' => [
                'id' => DatabaseUserProvider::class,
            ],
        ],
        'firewalls' => [
            'development' => [
                'pattern' => '^/(_(profiler|wdt))',
                'security' => false,
            ],
            'authentication' => [
                'stateless' => true,
                'pattern' => '^/api/auth/signin',
                'provider' => 'database',
                'user_checker' => PasswordAuthenticatedUserChecker::class,
                'custom_authenticators' => [
                    JsonLoginAuthenticator::class,
                ],
            ],
            'authorization' => [
                'stateless' => true,
                'pattern' => '^/api',
                'access_token' => [
                    'token_handler' => JwtAccessTokenHandler::class,
                ],
            ],
        ],
        'role_hierarchy' => [
            AccountRole::ADMIN => [
                AccountRole::USER,
            ],
        ],
    ]);

    if ($configurator->env() === 'test') {
        $configurator->extension(namespace: 'security', config: [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => 'plaintext',
            ],
        ]);
    }
};
