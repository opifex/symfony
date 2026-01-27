<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\HttpKernel\Exception\RequestParamTypeException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolationList;

#[AllowDynamicProperties]
final class RequestParamTypeExceptionTest extends TestCase
{
    public function testCreateWithEmptyExpectedValue(): void
    {
        $exception = RequestParamTypeException::create(expected: null, path: null);

        $this->assertEquals($exception->getViolations(), new ConstraintViolationList());
    }
}
