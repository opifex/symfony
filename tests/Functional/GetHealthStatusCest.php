<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Entity\HealthStatus;
use Codeception\Util\HttpCode;
use Tests\Support\FunctionalTester;

final class GetHealthStatusCest
{
    public function getSuccessHealthStatus(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendGet(url: '/api/health');
        $i->seeResponseCodeIs(code: HttpCode::OK);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsJson();
        $i->seeResponseContainsJson(['status' => HealthStatus::OK]);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'GetHealthStatusSchema.json'));
    }

    public function getHealthStatusUsingInvalidMethod(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendPost(url: '/api/health');
        $i->seeResponseCodeIs(code: HttpCode::METHOD_NOT_ALLOWED);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function getHealthStatusUsingInvalidRoute(FunctionalTester $i): void
    {
        $i->haveHttpHeaderApplicationJson();
        $i->sendGet(url: '/api/invalid');
        $i->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $i->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $i->seeResponseIsValidOnJsonSchema($i->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
