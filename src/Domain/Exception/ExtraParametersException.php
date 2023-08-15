<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

#[WithHttpStatus(statusCode: Response::HTTP_BAD_REQUEST)]
class ExtraParametersException extends ValidationFailedException
{
    /**
     * @param string[] $extraAttributes
     */
    public function __construct(array $extraAttributes, ?string $root)
    {
        $constraint = new ConstraintViolationList();

        foreach ($extraAttributes as $attribute) {
            $constraint->add(
                new ConstraintViolation(
                    message: 'This field was not expected.',
                    messageTemplate: null,
                    parameters: [],
                    root: $root,
                    propertyPath: $attribute,
                    invalidValue: null,
                ),
            );
        }

        parent::__construct($constraint);
    }
}
