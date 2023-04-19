<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Exception\Serializer\ExtraAttributesHttpException;
use App\Domain\Exception\Serializer\NormalizationFailedHttpException;
use App\Domain\Exception\Serializer\ValidationFailedHttpException;
use Codeception\Test\Unit;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

final class ExceptionTest extends Unit
{
    public function testExtraAttributesHttpException(): void
    {
        $extraAttributes = ['attribute1', 'attribute2', 'attribute3'];
        $exception = new ExtraAttributesHttpException($extraAttributes);

        $this->assertEquals(expected: Response::HTTP_BAD_REQUEST, actual: $exception->getStatusCode());
    }

    public function testNormalizationFailedHttpException(): void
    {
        $exception = new NormalizationFailedHttpException(['string'], path: 'parameter');

        $this->assertEquals(expected: Response::HTTP_BAD_REQUEST, actual: $exception->getStatusCode());
    }

    public function testValidationFailedHttpException(): void
    {
        $constraint = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'The violation message',
                messageTemplate: null,
                parameters: [],
                root: [],
                propertyPath: '',
                invalidValue: [],
            ),
        ]);
        $exception = new ValidationFailedHttpException($constraint);

        $this->assertEquals(expected: Response::HTTP_BAD_REQUEST, actual: $exception->getStatusCode());
    }
}
