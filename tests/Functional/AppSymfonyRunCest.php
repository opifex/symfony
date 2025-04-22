<?php

declare(strict_types=1);

namespace Tests\Functional;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Tests\Support\FunctionalTester;

final class AppSymfonyRunCest
{
    public function runCommandUsingValidParameters(FunctionalTester $I): void
    {
        $I->haveCleanMockServer();
        $I->haveMockResponse(
            request: Request::create(uri: getenv(name: 'HTTPBIN_URL') . 'json'),
            response: new JsonResponse(
                data: $I->getResponseContent(filename: 'HttpbinResponderGetJsonResponse.json'),
                json: true,
            ),
        );
        $I->runSymfonyConsoleCommand(
            command: 'app:symfony:run',
            parameters: ['--delay' => 0],
            expectedExitCode: Command::SUCCESS,
        );
    }
}
