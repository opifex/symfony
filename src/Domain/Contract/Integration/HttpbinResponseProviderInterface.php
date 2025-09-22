<?php

declare(strict_types=1);

namespace App\Domain\Contract\Integration;

use App\Domain\Exception\Integration\HttpbinResponseProviderException;

interface HttpbinResponseProviderInterface
{
    /**
     * @return array{
     *   slideshow: array{
     *     author: string,
     *     date: string,
     *     title: string,
     *     slides: array<int, array{
     *       title: string,
     *       type: string,
     *       items?: string[]
     *     }>
     *   }
     * }
     * @throws HttpbinResponseProviderException
     */
    public function getJson(): array;
}
