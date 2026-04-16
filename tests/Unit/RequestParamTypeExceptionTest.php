<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\HttpKernel\Exception\RequestParamTypeException;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;

#[AllowDynamicProperties]
#[AllowMockObjectsWithoutExpectations]
final class RequestParamTypeExceptionTest extends TestCase
{
    public function testCreateWithEmptyExpectedValue(): void
    {
        $exception = RequestParamTypeException::create(expected: null, path: null);

        self::assertEquals($exception->getViolations(), new ConstraintViolationList());
    }
}
