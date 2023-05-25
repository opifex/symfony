<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ExtraAttributesHttpException extends ValidationFailedHttpException
{
    /**
     * @param string[] $extraAttributes
     */
    public function __construct(array $extraAttributes)
    {
        $constraint = new ConstraintViolationList();

        foreach ($extraAttributes as $attribute) {
            $constraint->add(
                new ConstraintViolation(
                    message: new TranslatableMessage(
                        message: 'This field was not expected.',
                        domain: 'validators+intl-icu',
                    ),
                    messageTemplate: null,
                    parameters: [],
                    root: null,
                    propertyPath: $attribute,
                    invalidValue: null,
                ),
            );
        }

        parent::__construct($constraint);
    }
}
