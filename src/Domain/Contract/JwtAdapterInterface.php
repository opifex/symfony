<?php

declare(strict_types=1);

namespace App\Domain\Contract;

use App\Domain\Exception\JwtAdapterException;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\UserInterface;

interface JwtAdapterInterface
{
    /**
     * @throws JwtAdapterException
     */
    public function extractIdentifier(#[SensitiveParameter] string $accessToken): string;

    public function generateToken(UserInterface $user): string;
}
