<?php

declare(strict_types=1);

namespace App\Domain\Response;

use App\Domain\Entity\Account;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

final class GetAccountsByCriteriaItem
{
    #[Groups([GetAccountsByCriteriaResponse::GROUP_VIEW])]
    public readonly string $uuid;

    #[Groups([GetAccountsByCriteriaResponse::GROUP_VIEW])]
    public readonly string $email;

    #[Groups([GetAccountsByCriteriaResponse::GROUP_VIEW])]
    public readonly string $status;

    /**
     * @var string[]
     */
    #[Groups([GetAccountsByCriteriaResponse::GROUP_VIEW])]
    public readonly array $roles;

    #[Groups([GetAccountsByCriteriaResponse::GROUP_VIEW])]
    public readonly ?DateTimeImmutable $createdAt;

    #[Groups([GetAccountsByCriteriaResponse::GROUP_VIEW])]
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
