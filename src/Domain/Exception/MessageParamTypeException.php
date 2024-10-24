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
class MessageParamTypeException extends ValidationFailedException
{
    /**
     * @param string[]|null $expected
     */
    public function __construct(?array $expected, ?string $path, ?string $root)
    {
        $constraint = new ConstraintViolationList();

        if ($expected !== null && $path !== null) {
            $constraint->add(
                new ConstraintViolation(
                    message: 'This value should be of type {type}.',
                    messageTemplate: null,
                    parameters: ['type' => implode(separator: ', ', array: $expected)],
                    root: $root,
                    propertyPath: $path,
                    invalidValue: null,
                ),
            );
        }

        parent::__construct($constraint);
    }
}
