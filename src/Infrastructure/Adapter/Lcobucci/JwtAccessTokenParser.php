<?php

declare(strict_types=1);

namespace App\Infrastructure\Adapter\Lcobucci;

use App\Infrastructure\Adapter\Lcobucci\Exception\InvalidTokenException;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Token\RegisteredClaims;
use SensitiveParameter;

final readonly class JwtAccessTokenParser
{
    public function __construct(
        private JwtConfigurationBag $jwtConfigurationBag,
    ) {
    }

    public function parse(#[SensitiveParameter] string $accessToken): JwtAccessToken
    {
        $configuration = $this->jwtConfigurationBag->configuration();

        if ($accessToken === '') {
            throw InvalidTokenException::tokenHaveEmptyContent();
        }

        try {
            /** @var Plain $token */
            $token = $configuration->parser()->parse($accessToken);
        } catch (CannotDecodeContent $exception) {
            throw InvalidTokenException::errorWhileDecodingToken($exception);
        } catch (InvalidTokenStructure $exception) {
            throw InvalidTokenException::tokenHaveInvalidStructure($exception);
        }

        if (!$configuration->validator()->validate($token, ...$configuration->validationConstraints())) {
            throw InvalidTokenException::tokenIsInvalidOrExpired();
        }

        /** @var string $userIdentifier */
        $userIdentifier = $token->claims()->get(name: RegisteredClaims::SUBJECT) ?? '';
        /** @var string[] $userRoles */
        $userRoles = $token->claims()->get(name: JwtRegisteredClaims::ROLES) ?? [];

        return new JwtAccessToken($userIdentifier, $userRoles);
    }
}
