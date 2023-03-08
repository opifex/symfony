<?php

declare(strict_types=1);

namespace App\Domain\Contract\Message;

interface MessageInterface
{
    public const COMMAND = 'command.bus';

    public const QUERY = 'query.bus';

    public const GROUP_BODY = 'body.group';

    public const GROUP_URL = 'url.group';
}
