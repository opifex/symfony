<?php

declare(strict_types=1);

namespace App\Tests;

final class LifecycleRoutingCest
{
    public function getInvalidRoute(FunctionalTester $i): void
    {
        $i->sendGet(url: '/api/invalid');
        $i->seeResponseCodeIsClientError();
    }
}
