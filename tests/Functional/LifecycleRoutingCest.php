<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;
use Codeception\Util\HttpCode;

final class LifecycleRoutingCest
{
    public function getInvalidRoute(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendGet(url: '/api/invalid');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
    }
}
