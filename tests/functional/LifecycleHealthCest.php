<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Entity\HealthStatus;
use Codeception\Util\HttpCode;

final class LifecycleHealthCest
{
    public function checkHealthStatus(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->haveHttpHeader(name: 'X-Request-Id', value: '9bc545a3-2492-440c-9529-0b616a0475f0');
        $i->sendGet(url: '/api/health');
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['status' => HealthStatus::Ok->value]);
        $i->seeHttpHeader(name: 'X-Request-Id', value: '9bc545a3-2492-440c-9529-0b616a0475f0');
    }

    public function checkHealthStatusUsingInvalidMethod(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/health');
        $i->seeResponseCodeIs(code: HttpCode::METHOD_NOT_ALLOWED);
    }
}
