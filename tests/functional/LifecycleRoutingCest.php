<?php

declare(strict_types=1);

namespace App\Tests;

use Codeception\Attribute\Before;
use Codeception\Util\HttpCode;

final class LifecycleRoutingCest
{
    #[Before('prepareHttpHeaders')]
    public function getInvalidRoute(FunctionalTester $i): void
    {
        $i->sendGet(url: '/api/invalid');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
    }

    protected function prepareHttpHeaders(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }
}
