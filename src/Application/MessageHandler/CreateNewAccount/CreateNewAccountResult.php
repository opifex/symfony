<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Model\Account;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class CreateNewAccountResult extends JsonResponse
{
    public static function success(Account $account): self
    {
        return new self(
            data: [
                'uuid' => $account->id,
            ],
            status: Response::HTTP_CREATED,
        );
    }
}
