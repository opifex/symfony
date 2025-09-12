<?php

declare(strict_types=1);

namespace Tests\Functional;

use Override;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Support\DatabaseEntityManagerTrait;
use Tests\Support\HttpClientAuthorizationTrait;
use Tests\Support\HttpClientRequestTrait;

abstract class AbstractWebTestCase extends WebTestCase
{
    use DatabaseEntityManagerTrait;
    use HttpClientRequestTrait;
    use HttpClientAuthorizationTrait;

    #[Override]
    protected function setUp(): void
    {
        $this->createClient();
        $this->purgeDatabase();
    }
}
