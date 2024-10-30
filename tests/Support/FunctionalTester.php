<?php

declare(strict_types=1);

namespace Tests\Support;

use Codeception\Actor;
use Codeception\Util\HttpCode;
use Exception;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends Actor
{
    use _generated\FunctionalTesterActions;

    public function getSchemaPath(string $schema): string
    {
        return codecept_data_dir() . 'Schema/' . $schema;
    }

    public function haveHttpHeaderApplicationJson(): void
    {
        $this->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }

    /**
     * @throws Exception
     */
    public function haveHttpHeaderAuthorizationAdmin(string $email, string $password): void
    {
        $this->haveHttpHeaderApplicationJson();
        $this->sendPost(url: '/api/auth/signin', params: json_encode(['email' => $email, 'password' => $password]));
        $this->seeResponseCodeIs(code: HttpCode::OK);
        $this->seeResponseIsJson();

        $accessToken = current($this->grabDataFromResponseByJsonPath(jsonPath: '$access_token'));

        $this->haveHttpHeader(name: 'Authorization', value: 'Bearer ' . $accessToken);
    }
}
