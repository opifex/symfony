<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountById;

use App\Domain\Entity\Account;
use DateTimeInterface;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class GetAccountByIdResponse extends JsonResponse
{
    public static function create(Account $account): self
    {
        return new self(
            data: [
                'uuid' => $account->getUuid(),
                'email' => $account->getEmail(),
                'locale' => $account->getLocale(),
                'status' => $account->getStatus()->value,
                'roles' => $account->getRoles(),
                'created_at' => $account->getCreatedAt()->format(format: DateTimeInterface::ATOM),
            ],
            status: Response::HTTP_OK,
        );
    }
}
