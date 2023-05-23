<?php

declare(strict_types=1);

namespace App\Domain\Response;

use App\Domain\Entity\Account;
use App\Domain\Response\Account\GetAccountByIdResponse;
use App\Domain\Response\Account\GetAccountsByCriteriaResponse;
use App\Domain\Response\Auth\GetSigninAccountInfoResponse;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

abstract class AbstractAccountResponse
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
        $this->status = $account->getStatus();
        $this->updatedAt = $account->getUpdatedAt();
        $this->uuid = $account->getUuid();
    }
}
