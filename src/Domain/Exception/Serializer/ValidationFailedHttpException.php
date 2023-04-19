<?php

declare(strict_types=1);

namespace App\Domain\Exception\Serializer;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationFailedHttpException extends AbstractHttpException
{
    protected string $exception = 'Parameters validation failed.';

    public function __construct(ConstraintViolationListInterface $constraint)
    {
        $context = [];

        foreach ($constraint as $item) {
            if ($item instanceof ConstraintViolationInterface) {
                $object = is_object($item->getRoot()) ? $item->getRoot()::class : null;
                $parameter = (new UnicodeString($item->getPropertyPath()))->snake()->toString();
                $context['validation'][] = [
                    'name' => $parameter,
                    'reason' => $item->getMessage(),
                    'object' => $object,
                    'value' => $item->getInvalidValue(),
                ];
            }
        }

        parent::__construct(statusCode: Response::HTTP_BAD_REQUEST, message: $this->exception, context: $context);
    }
}
