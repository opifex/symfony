<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Domain\Model\HealthStatus;
use Codeception\Util\HttpCode;
use Tests\Support\FunctionalTester;

final class GetHealthStatusCest
{
    public function ensureHealthStatusIsOk(FunctionalTester $I): void
    {
        $I->haveHttpHeaderApplicationJson();
        $I->sendGet(url: '/api/health');
        $I->seeResponseCodeIs(code: HttpCode::OK);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['status' => HealthStatus::Ok->toString()]);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'GetHealthStatusSchema.json'));
    }

    public function tryToGetHealthWithInvalidMethod(FunctionalTester $I): void
    {
        $I->haveHttpHeaderApplicationJson();
        $I->sendPost(url: '/api/health');
        $I->seeResponseCodeIs(code: HttpCode::METHOD_NOT_ALLOWED);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }

    public function tryToGetHealthWithInvalidRoute(FunctionalTester $I): void
    {
        $I->haveHttpHeaderApplicationJson();
        $I->sendGet(url: '/api/invalid');
        $I->seeResponseCodeIs(code: HttpCode::NOT_FOUND);
        $I->seeRequestTimeIsLessThan(expectedMilliseconds: 300);
        $I->seeResponseIsValidOnJsonSchema($I->getSchemaPath(filename: 'ApplicationExceptionSchema.json'));
    }
}
