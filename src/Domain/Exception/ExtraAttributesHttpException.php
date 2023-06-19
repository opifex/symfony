<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ExtraAttributesHttpException extends ValidationFailedHttpException
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
