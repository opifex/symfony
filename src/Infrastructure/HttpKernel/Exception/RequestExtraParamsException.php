<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpKernel\Exception;

use App\Infrastructure\Messenger\Exception\ValidationFailedException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
class RequestExtraParamsException extends ValidationFailedException
{
    /**
     * @param string[] $extraAttributes
     */
    public static function create(array $extraAttributes, ?string $root = null): self
    {
        $violations = new ConstraintViolationList(
            violations: array_map(
                callback: static fn(string $attribute): ConstraintViolation => new ConstraintViolation(
                    message: 'This field was not expected.',
                    messageTemplate: null,
                    parameters: [],
                    root: $root,
                    propertyPath: $attribute,
                    invalidValue: null,
                ),
                array: $extraAttributes,
            ),
        );

        return new self($violations);
    }
}
