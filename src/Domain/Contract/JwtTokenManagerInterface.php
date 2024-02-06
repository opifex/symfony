<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\JwtTokenManagerException;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\UserInterface;

interface JwtTokenManagerInterface
{
    /**
     * @throws JwtTokenManagerException
     */
    public function extractUserIdentifier(#[SensitiveParameter] string $accessToken): string;

    public function generateToken(UserInterface $user): string;
}
