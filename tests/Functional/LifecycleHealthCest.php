<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Entity\HealthStatus;
use Codeception\Util\HttpCode;
use Tests\Support\FunctionalTester;

final class LifecycleHealthCest
{
    public function checkHealthStatus(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendGet(url: '/api/health');
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['status' => HealthStatus::Ok->value]);
    }

    public function checkHealthStatusUsingInvalidMethod(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/health');
        $i->seeResponseCodeIs(code: HttpCode::METHOD_NOT_ALLOWED);
    }
}
