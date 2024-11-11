<?php

declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tests\Support\FunctionalTester;

final class AppSymfonyRunCest
{
    public function runCommandUsingValidParameters(FunctionalTester $i): void
    {
        $i->haveCleanMockServer();
        $i->haveMockResponse(
            request: Request::create(uri: getenv(name: 'HTTPBIN_URL') . 'json'),
            response: new JsonResponse(
                data: $i->getResponseContent(filename: 'HttpbinResponderGetJsonResponse.json'),
                json: true,
            ),
        );
        $i->runSymfonyConsoleCommand(
            command: 'app:symfony:run',
            parameters: ['--count' => 1, '--delay' => 0],
            expectedExitCode: Command::SUCCESS,
        );
    }

    public function runCommandUsingInvalidParameters(FunctionalTester $i): void
    {
        $i->runSymfonyConsoleCommand(
            command: 'app:symfony:run',
            parameters: ['--count' => -1, '--delay' => -1],
            expectedExitCode: Command::FAILURE,
        );
    }
}
