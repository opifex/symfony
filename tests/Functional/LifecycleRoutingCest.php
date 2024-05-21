<?php

declare(strict_types=1);

namespace Tests\Functional;

use Codeception\Util\HttpCode;
use Tests\Support\FunctionalTester;

final class LifecycleRoutingCest
{
    public function getInvalidRoute(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendGet(url: '/api/invalid');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
    }
}
