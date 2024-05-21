<?php

declare(strict_types=1);

namespace Tests\Support;

use Codeception\Actor;
use Codeception\Util\HttpCode;

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

    public function getDefaultPassword(): string
    {
        return 'password4#account';
    }

    public function haveHttpHeaderApplicationJson(): void
    {
        $this->haveHttpHeader(name: 'Content-Type', value: 'application/json');
    }

    public function haveHttpHeaderAuthorizationAdmin(): void
    {
        $this->haveHttpHeaderApplicationJson();
        $this->sendPost(
            url: '/api/auth/signin',
            params: json_encode([
                'email' => 'admin@example.com',
                'password' => $this->getDefaultPassword(),
            ]),
        );
        $this->seeResponseCodeIs(code: HttpCode::OK);
        $this->seeResponseIsJson();

        $accessToken = current($this->grabDataFromResponseByJsonPath(jsonPath: '$access_token'));

        $this->haveHttpHeader(name: 'Authorization', value: 'Bearer ' . $accessToken);
    }
}
