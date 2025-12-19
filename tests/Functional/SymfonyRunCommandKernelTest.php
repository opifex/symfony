<?php

declare(strict_types=1);

namespace Tests\Functional;

use Override;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpFoundation\Request;
use Tests\Support\HttpMockClientResponse;
use Tests\Support\HttpMockClientTrait;

final class SymfonyRunCommandKernelTest extends KernelTestCase
{
    use HttpMockClientTrait;

    private readonly Command $command;

    #[Override]
    protected function setUp(): void
    {
        $application = new Application(self::bootKernel());
        $this->command = $application->find(name: 'app:symfony:run');
    }

    public function testEnsureConsoleCommandExecutesSuccessfully(): void
    {
        $this->loadMockResponses([
            new HttpMockClientResponse(
                requestMethod: Request::METHOD_GET,
                requestUrl: 'https://httpbin.org/json',
                responseBody: $this->getResponseFromFile(file: 'HttpbinGetJsonResponse.json'),
            ),
        ]);
        $commandTester = new CommandTester($this->command);
        $commandTester->execute(['--delay' => 0]);
        $commandTester->assertCommandIsSuccessful();
        $this->assertStringContainsString(needle: 'Symfony console command', haystack: $commandTester->getDisplay());
        $this->assertStringContainsString(needle: 'Success', haystack: $commandTester->getDisplay());
    }
}
