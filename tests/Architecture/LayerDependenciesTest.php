<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPat\Selector\Selector;
use PHPat\Test\Builder\Rule;
use PHPat\Test\PHPat;

final class LayerDependenciesTest
{
    public function testApplicationDependencies(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(namespace: 'App\Application'))
            ->shouldNot()
            ->dependOn()
            ->classes(
                Selector::AllOf(
                    Selector::inNamespace(namespace: 'App'),
                    Selector::NoneOf(
                        Selector::inNamespace(namespace: 'App\Application'),
                        Selector::inNamespace(namespace: 'App\Domain'),
                    ),
                ),
            )
            ->because(tips: 'Application may depend only on Application and Domain.');
    }

    public function testDomainDependencies(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(namespace: 'App\Domain'))
            ->shouldNot()
            ->dependOn()
            ->classes(
                Selector::AllOf(
                    Selector::inNamespace(namespace: 'App'),
                    Selector::Not(Selector::inNamespace(namespace: 'App\Domain')),
                ),
            )
            ->because(tips: 'Domain may depend only on Domain.');
    }

    public function testInfrastructureDependencies(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(namespace: 'App\Infrastructure'))
            ->shouldNot()
            ->dependOn()
            ->classes(
                Selector::AllOf(
                    Selector::inNamespace(namespace: 'App'),
                    Selector::NoneOf(
                        Selector::inNamespace(namespace: 'App\Infrastructure'),
                        Selector::inNamespace(namespace: 'App\Application'),
                        Selector::inNamespace(namespace: 'App\Domain'),
                    ),
                ),
            )
            ->because(tips: 'Infrastructure may depend only on Infrastructure, Application and Domain.');
    }

    public function testPresentationDependencies(): Rule
    {
        return PHPat::rule()
            ->classes(Selector::inNamespace(namespace: 'App\Presentation'))
            ->shouldNot()
            ->dependOn()
            ->classes(
                Selector::AllOf(
                    Selector::inNamespace(namespace: 'App'),
                    Selector::NoneOf(
                        Selector::inNamespace(namespace: 'App\Presentation'),
                        Selector::inNamespace(namespace: 'App\Application'),
                        Selector::inNamespace(namespace: 'App\Domain'),
                    ),
                ),
            )
            ->because(tips: 'Presentation may depend only on Presentation, Application and Domain.');
    }
}
