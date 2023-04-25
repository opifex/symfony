<?php

declare(strict_types=1);

namespace App\Domain\Contract\Message;

interface MessageInterface
{
    final public const COMMAND = 'command.bus';

    final public const QUERY = 'query.bus';

    final public const BODY_PARAM = 'body.param';

    final public const URL_PARAM = 'url.param';
}
