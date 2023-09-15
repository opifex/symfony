<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Entity\HealthStatus;
use Codeception\Attribute\Before;
use Codeception\Util\HttpCode;

final class LifecycleHealthCest
{
    #[Before('prepareHttpHeaders')]
    public function checkHealthStatus(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'X-Request-Id', value: '9bc545a3-2492-440c-9529-0b616a0475f0');
        $i->sendGet(url: '/api/health');
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['status' => HealthStatus::OK->value]);
        $i->seeHttpHeader(name: 'X-Request-Id', value: '9bc545a3-2492-440c-9529-0b616a0475f0');
    }

    #[Before('prepareHttpHeaders')]
    public function checkHealthStatusUsingInvalidMethod(FunctionalTester $i): void
    {
        $i->sendPost(url: '/api/health');
        $i->seeResponseCodeIs(code: HttpCode::METHOD_NOT_ALLOWED);
    }

    protected function prepareHttpHeaders(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }
}
