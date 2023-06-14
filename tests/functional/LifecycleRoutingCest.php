<?php

declare(strict_types=1);

namespace App\Tests;

final class LifecycleRoutingCest
{
    public function getInvalidRoute(FunctionalTester $i): void
    {
        $i->haveHttpHeader(name: 'Content-Type', value: 'application/json');
        $i->sendGet(url: '/api/invalid');
        $i->seeResponseCodeIsClientError();
    }
}
