<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\SigninIntoAccount;

use App\Domain\Contract\JwtTokenManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SigninIntoAccountHandler
{
    public function __construct(private readonly JwtTokenManagerInterface $jwtTokenManager)
    {
    }

    public function __invoke(SigninIntoAccountRequest $message): SigninIntoAccountResponse
    {
        $accessToken = $this->jwtTokenManager->generateToken($message->email);

        return new SigninIntoAccountResponse($accessToken);
    }
}
