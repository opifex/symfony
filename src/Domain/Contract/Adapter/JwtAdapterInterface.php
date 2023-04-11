<?php

declare(strict_types=1);

namespace App\Domain\Contract\Adapter;

use App\Domain\Exception\TokenAdapterException;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\UserInterface;

interface JwtAdapterInterface
{
    /**
     * @throws TokenAdapterException
     */
    public function extractIdentifier(#[SensitiveParameter] string $accessToken): string;

    public function generateToken(UserInterface $user): string;
}
