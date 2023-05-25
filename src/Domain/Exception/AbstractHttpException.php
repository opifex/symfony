<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class AbstractHttpException extends HttpException
{
    /**
     * @param int $statusCode
     * @param string $message
     * @param array&array<string, mixed> $context
     */
    public function __construct(int $statusCode, string $message, private array $context = [])
    {
        parent::__construct($statusCode, $message);
    }

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
