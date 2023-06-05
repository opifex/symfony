<?php

declare(strict_types=1);

namespace App\Domain\Response;

use App\Domain\Entity\Account;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

final class GetAccountByIdResponse
{
    public const GROUP_VIEW = __CLASS__ . ':view';

    #[Groups([self::GROUP_VIEW])]
    public readonly string $uuid;

    #[Groups([self::GROUP_VIEW])]
    public readonly string $email;

    #[Groups([self::GROUP_VIEW])]
    public readonly string $status;

    /**
     * @var string[]
     */
    #[Groups([self::GROUP_VIEW])]
    public readonly array $roles;

    #[Groups([self::GROUP_VIEW])]
    public readonly ?DateTimeImmutable $createdAt;

    #[Groups([self::GROUP_VIEW])]
    public readonly ?DateTimeImmutable $updatedAt;

    public function __construct(Account $account)
    {
        $this->createdAt = $account->getCreatedAt();
        $this->email = $account->getEmail();
        $this->roles = $account->getRoles();
        $this->status = $account->getStatus();
        $this->updatedAt = $account->getUpdatedAt();
        $this->uuid = $account->getUuid();
    }
}
