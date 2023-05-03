<?php

declare(strict_types=1);

namespace App\Domain\Response\Account;

use App\Domain\Entity\Account\Account;
use App\Domain\Response\GetAccountByIdResponse;
use App\Domain\Response\GetAccountsByCriteriaResponse;
use App\Domain\Response\GetSigninAccountInfoResponse;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

class AccountResponseItem
{
    #[Groups([
        GetAccountByIdResponse::GROUP_VIEW,
        GetAccountsByCriteriaResponse::GROUP_VIEW,
        GetSigninAccountInfoResponse::GROUP_VIEW,
    ])]
    public readonly string $uuid;

    #[Groups([
        GetAccountByIdResponse::GROUP_VIEW,
        GetAccountsByCriteriaResponse::GROUP_VIEW,
        GetSigninAccountInfoResponse::GROUP_VIEW,
    ])]
    public readonly string $email;

    #[Groups([
        GetAccountByIdResponse::GROUP_VIEW,
        GetAccountsByCriteriaResponse::GROUP_VIEW,
        GetSigninAccountInfoResponse::GROUP_VIEW,
    ])]
    public readonly string $locale;

    #[Groups([
        GetAccountByIdResponse::GROUP_VIEW,
        GetAccountsByCriteriaResponse::GROUP_VIEW,
        GetSigninAccountInfoResponse::GROUP_VIEW,
    ])]
    public readonly string $status;

    /**
     * @var string[]
     */
    #[Groups([
        GetAccountByIdResponse::GROUP_VIEW,
        GetAccountsByCriteriaResponse::GROUP_VIEW,
        GetSigninAccountInfoResponse::GROUP_VIEW,
    ])]
    public readonly array $roles;

    #[Groups([
        GetAccountByIdResponse::GROUP_VIEW,
        GetAccountsByCriteriaResponse::GROUP_VIEW,
        GetSigninAccountInfoResponse::GROUP_VIEW,
    ])]
    public readonly ?DateTimeImmutable $createdAt;

    #[Groups([
        GetAccountByIdResponse::GROUP_VIEW,
        GetAccountsByCriteriaResponse::GROUP_VIEW,
        GetSigninAccountInfoResponse::GROUP_VIEW,
    ])]
    public readonly ?DateTimeImmutable $updatedAt;

    public function __construct(Account $account)
    {
        $this->createdAt = $account->getCreatedAt();
        $this->email = $account->getEmail();
        $this->roles = $account->getRoles();
        $this->locale = $account->getLocale();
        $this->status = $account->getStatus();
        $this->updatedAt = $account->getUpdatedAt();
        $this->uuid = $account->getUuid();
    }
}
