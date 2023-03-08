<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class NormalizationFailedHttpException extends ValidationFailedHttpException
{
    /**
     * @param string[]|null $expected
     */
    public function __construct(?array $expected, ?string $path)
    {
        $constraint = new ConstraintViolationList();

        if ($expected !== null && $path !== null) {
            $constraint->add(
                new ConstraintViolation(
                    message: new TranslatableMessage(
                        message: 'This value should be of type {type}.',
                        parameters: ['type' => implode(separator: ', ', array: $expected)],
                        domain: 'validators+intl-icu',
                    ),
                    messageTemplate: null,
                    parameters: [],
                    root: null,
                    propertyPath: $path,
                    invalidValue: null,
                ),
            );
        }

        parent::__construct($constraint);
    }
}
