<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountById;

use App\Domain\Model\Account;
use DateTimeInterface;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class GetAccountByIdResult extends JsonResponse
{
    public static function success(Account $account): self
    {
        return new self(
            data: [
                'uuid' => $account->id,
                'email' => $account->email,
                'locale' => $account->locale,
                'status' => $account->status,
                'roles' => $account->roles,
                'created_at' => $account->createdAt->format(format: DateTimeInterface::ATOM),
            ],
            status: Response::HTTP_OK,
        );
    }
}
