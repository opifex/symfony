<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountById;

use App\Domain\Model\Account;
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
                'id' => $account->getId()->toString(),
                'email' => $account->getEmail(),
                'locale' => $account->getLocale(),
                'status' => $account->getStatus(),
                'roles' => $account->getRoles(),
                'created_at' => $account->getCreatedAt()->toAtomString(),
            ],
            status: Response::HTTP_OK,
        );
    }
}
