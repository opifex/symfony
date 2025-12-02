<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpKernel\Exception;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[Exclude]
#[WithHttpStatus(statusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
class RequestParamTypeException extends RuntimeException
{
    public function __construct(
        private readonly ConstraintViolationListInterface $violations,
    ) {
        parent::__construct(message: 'Request contains parameters with invalid type.');
    }

    /**
     * @param string[]|null $expected
     */
    public static function create(?array $expected, ?string $path, ?string $root = null): self
    {
        if ($expected === null || $path === null) {
            return new self(new ConstraintViolationList());
        }

        $constraint = new ConstraintViolationList([
            new ConstraintViolation(
                message: 'This value should be of type {{ types }}.',
                messageTemplate: null,
                parameters: ['{{ types }}' => implode(separator: ', ', array: $expected)],
                root: $root,
                propertyPath: $path,
                invalidValue: null,
            ),
        ]);

        return new self($constraint);
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
