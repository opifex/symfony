<?php

declare(strict_types=1);

namespace App\Tests;

use Codeception\Actor;
use Codeception\Lib\Friend;
use Codeception\Util\HttpCode;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method Friend haveFriend($name, $actorClass = null)
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
        $this->seeResponseCodeIs(code: HttpCode::NO_CONTENT);
        $this->seeHttpHeader(name: 'Authorization');

        $authorizationHeader = $this->grabHttpHeader(name: 'Authorization');

        $this->haveHttpHeader(name: 'Authorization', value: $authorizationHeader);
    }
}
