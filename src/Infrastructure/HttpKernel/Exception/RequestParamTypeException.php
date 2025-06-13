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
class RequestParamTypeException extends ValidationFailedException
{
    /**
     * @param string[]|null $expected
     */
    public static function create(?array $expected, ?string $path, ?string $root = null): self
    {
        if (empty($expected) || $path === null) {
            return new self(new ConstraintViolationList());
        }

        $constraint = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'This value should be of type {type}.',
                messageTemplate: null,
                parameters: ['type' => implode(separator: ', ', array: $expected)],
                root: $root,
                propertyPath: $path,
                invalidValue: null,
            ),
        ]);

        return new self($constraint);
    }
}
