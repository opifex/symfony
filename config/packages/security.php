<?php

declare(strict_types=1);

use App\Domain\Model\Role;
use App\Infrastructure\Security\DatabaseUserProvider;
use App\Infrastructure\Security\JsonLoginAuthenticator;
use App\Infrastructure\Security\JwtAccessTokenHandler;
use App\Infrastructure\Security\PasswordAuthenticatedUserChecker;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Config\SecurityConfig;

return static function (ContainerConfigurator $configurator, SecurityConfig $security): void {
    $security->provider(name: 'database')->id(value: DatabaseUserProvider::class);

    $security->passwordHasher(class: PasswordAuthenticatedUserInterface::class)
        ->algorithm(value: 'auto');

    $security->firewall(name: 'development')
        ->pattern(value: '^/(_(profiler|wdt))')
        ->security(value: false);

    $security->firewall(name: 'authentication')
        ->stateless(value: true)
        ->pattern(value: '^/api/auth/signin')
        ->provider(value: 'database')
        ->userChecker(value: PasswordAuthenticatedUserChecker::class)
        ->customAuthenticators([JsonLoginAuthenticator::class]);

    $security->firewall(name: 'authorization')
        ->stateless(value: true)
        ->pattern(value: '^/api')
        ->accessToken()->tokenHandler(value: JwtAccessTokenHandler::class);

    $security->roleHierarchy(Role::Admin->value, Role::User->value);

    if ($configurator->env() === 'test') {
        $security->passwordHasher(class: PasswordAuthenticatedUserInterface::class)
            ->algorithm(value: 'plaintext');
    }
};
