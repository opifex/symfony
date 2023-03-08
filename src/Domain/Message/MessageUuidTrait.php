<?php

declare(strict_types=1);

namespace App\Domain\Message;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait MessageUuidTrait
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[Groups(MessageInterface::GROUP_URL)]
    public string $uuid = '';
}
