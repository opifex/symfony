<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Entity\HealthStatus;
use Codeception\Util\HttpCode;

final class LifecycleHealthCest
{
    public function checkHealthStatus(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');
        $i->haveHttpHeader(name: 'X-Request-Id', value: '9bc545a3-2492-440c-9529-0b616a0475f0');
        $i->sendGet(url: '/api/health');
        $i->seeResponseCodeIsSuccessful();
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['status' => HealthStatus::OK->value]);
        $i->seeHttpHeader(name: 'X-Request-Id', value: '9bc545a3-2492-440c-9529-0b616a0475f0');
    }

    public function checkHealthStatusUsingInvalidMethod(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');
        $i->sendPost(url: '/api/health');
        $i->seeResponseCodeIs(code: HttpCode::METHOD_NOT_ALLOWED);
    }
}
