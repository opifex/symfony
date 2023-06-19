<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationFailedHttpException extends HttpException
{
    public function __construct(private ConstraintViolationListInterface $violations)
    {
        parent::__construct(statusCode: Response::HTTP_BAD_REQUEST, message: 'Parameters validation failed.');
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
