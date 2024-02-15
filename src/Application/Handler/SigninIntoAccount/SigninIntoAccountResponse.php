<?php

declare(strict_types=1);

namespace App\Application\Handler\SigninIntoAccount;

use App\Domain\Entity\AuthorizationToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: Response::HTTP_OK)]
final class SigninIntoAccountResponse
{
    public readonly string $accessToken;

    public function __construct(AuthorizationToken $authorizationToken)
    {
        $this->accessToken = $authorizationToken->getSecret();
    }
}
