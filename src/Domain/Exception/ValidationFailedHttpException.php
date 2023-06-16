<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\String\UnicodeString;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationFailedHttpException extends AbstractHttpException
{
    protected string $exception = 'Parameters validation failed.';

    public function __construct(ConstraintViolationListInterface $constraint, bool $debug = false)
    {
        $context = [];

        foreach ($constraint as $item) {
            if ($item instanceof ConstraintViolationInterface) {
                $parameter = (new UnicodeString($item->getPropertyPath()))->snake()->toString();
                $validation = ['name' => $parameter, 'reason' => $item->getMessage()];

                if ($debug) {
                    $validation['object'] = match (true) {
                        is_object($item->getRoot()) => $item->getRoot()::class,
                        is_string($item->getRoot()) => $item->getRoot(),
                        default => null,
                    };
                    $validation['value'] = $item->getInvalidValue();
                }

                $context['validation'][] = $validation;
            }
        }

        parent::__construct(statusCode: Response::HTTP_BAD_REQUEST, message: $this->exception, context: $context);
    }
}
