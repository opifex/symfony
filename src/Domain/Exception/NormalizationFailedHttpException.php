<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class NormalizationFailedHttpException extends ValidationFailedHttpException
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
                    message: 'This value should have a valid type.',
                    messageTemplate: 'This value should be of type {type}.',
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
