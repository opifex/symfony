<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetSigninAccount;

use App\Domain\Model\Account;
use DateTimeInterface;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

#[Exclude]
final class GetSigninAccountResult extends JsonResponse
{
    public static function success(Account $account): self
    {
        return new self(
            data: [
                'id' => $account->getId(),
                'email' => $account->getEmail(),
                'locale' => $account->getLocale(),
                'status' => $account->getStatus(),
                'roles' => $account->getRoles(),
                'created_at' => $account->getCreatedAt()->format(format: DateTimeInterface::ATOM),
            ],
            status: Response::HTTP_OK,
        );
    }
}
