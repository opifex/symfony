<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Infrastructure\HttpKernel\Exception\RequestParamTypeException;
use Codeception\Test\Unit;
use Symfony\Component\Validator\ConstraintViolationList;

final class RequestParamTypeExceptionTest extends Unit
{
    public function testCreateWithEmptyExpectedValue(): void
    {
        $exception = RequestParamTypeException::create(expected: null, path: null);

        $this->assertEquals($exception->getViolations(), new ConstraintViolationList());
    }
}
