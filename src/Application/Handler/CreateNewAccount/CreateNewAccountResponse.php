<?php

declare(strict_types=1);

namespace App\Application\Handler\CreateNewAccount;

use App\Domain\Entity\Account;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(statusCode: Response::HTTP_CREATED)]
final class CreateNewAccountResponse
{
    public readonly string $uuid;

    public function __construct(Account $account)
    {
        $this->uuid = $account->getUuid();
    }
}
