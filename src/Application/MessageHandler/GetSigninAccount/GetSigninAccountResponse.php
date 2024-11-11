<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetSigninAccount;

use App\Domain\Entity\Account;
use DateTimeInterface;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class GetSigninAccountResponse
{
    public readonly string $uuid;

    public readonly string $email;

    public readonly string $locale;

    public readonly string $status;

    /** @var string[] */
    public readonly array $roles;

    public readonly DateTimeInterface $createdAt;

    public function __construct(Account $account)
    {
        $this->uuid = $account->getUuid();
        $this->email = $account->getEmail();
        $this->locale = $account->getLocale();
        $this->status = $account->getStatus();
        $this->roles = $account->getRoles();
        $this->createdAt = $account->getCreatedAt();
    }
}
