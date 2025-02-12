<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
class ValidationFailedException extends RuntimeException
{
    public function __construct(
        private readonly ConstraintViolationListInterface $violations,
    ) {
        parent::__construct(message: 'Parameters validation failed.');
    }

    public static function fromViolations(ConstraintViolationListInterface $violations): self
    {
        return new self($violations);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
