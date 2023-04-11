<?php

declare(strict_types=1);

namespace App\Domain\Contract\Adapter;

use App\Domain\Exception\Adapter\JwtAdapterException;
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
