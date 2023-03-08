<?php

declare(strict_types=1);

namespace App\Domain\Message\Account;

use App\Domain\Contract\Message\MessageInterface;
use App\Domain\Message\MessageUuidTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class ApplyAccountActionCommand implements MessageInterface
{
    use MessageUuidTrait;

    #[Assert\NotBlank]
    #[Groups(self::GROUP_URL)]
    public string $action;
}
