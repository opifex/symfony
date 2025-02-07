<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
class MessageExtraParamsException extends ValidationFailedException
{
    /**
     * @param string[] $extraAttributes
     */
    public static function create(array $extraAttributes, ?string $root = null): self
    {
        $violations = new ConstraintViolationList(
            violations: array_map(
                callback: fn(string $attribute) => new ConstraintViolation(
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
