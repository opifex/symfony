<?php

declare(strict_types=1);

namespace App\Tests;

use App\Application\Serializer\ExceptionNormalizer;
use Codeception\Test\Unit;
use Exception;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Exception\ValidationFailedException as MessengerValidationFailedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\Exception\ExtraAttributesException as SerializerExtraAttributesException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException as ValidatorValidationFailedException;
use Throwable;

final class ExceptionNormalizerTest extends Unit
{
    private ConstraintViolationList $violations;

    private ExceptionNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new ExceptionNormalizer();
        $this->violations = new ConstraintViolationList();
    }

    public function testNormalizeObject(): void
    {
        $this->assertTrue($this->normalizer->supportsNormalization(new Exception()));
        $this->assertFalse($this->normalizer->supportsNormalization(data: null));

        $supportedTypes = $this->normalizer->getSupportedTypes(format: null);

        $this->assertEquals(expected: [Throwable::class => true], actual: $supportedTypes);

        $normalized = $this->normalizer->normalize(object: null);
        $this->assertIsArray($normalized);
        $this->assertEquals(expected: Response::HTTP_INTERNAL_SERVER_ERROR, actual: $normalized['code']);

        $exception = new AuthenticationException(
            message: 'Full authentication is required to access this resource.',
            code: Response::HTTP_UNAUTHORIZED,
        );
        $normalized = $this->normalizer->normalize($exception);
        $this->assertIsArray($normalized);
        $this->assertEquals(expected: Response::HTTP_UNAUTHORIZED, actual: $normalized['code']);

        $exception = new HttpException(statusCode: Response::HTTP_METHOD_NOT_ALLOWED);
        $normalized = $this->normalizer->normalize($exception);
        $this->assertIsArray($normalized);
        $this->assertEquals(expected: Response::HTTP_METHOD_NOT_ALLOWED, actual: $normalized['code']);

        $exception = new MessengerValidationFailedException(new stdClass(), $this->violations);
        $normalized = $this->normalizer->normalize($exception);
        $this->assertIsArray($normalized);
        $this->assertEquals(expected: Response::HTTP_BAD_REQUEST, actual: $normalized['code']);

        $exception = new NotNormalizableValueException();
        $normalized = $this->normalizer->normalize($exception);
        $this->assertIsArray($normalized);
        $this->assertEquals(expected: Response::HTTP_BAD_REQUEST, actual: $normalized['code']);

        $exception = new SerializerExtraAttributesException([]);
        $normalized = $this->normalizer->normalize($exception);
        $this->assertIsArray($normalized);
        $this->assertEquals(expected: Response::HTTP_BAD_REQUEST, actual: $normalized['code']);

        $exception = new ValidatorValidationFailedException(value: null, violations: $this->violations);
        $normalized = $this->normalizer->normalize($exception);
        $this->assertIsArray($normalized);
        $this->assertEquals(expected: Response::HTTP_BAD_REQUEST, actual: $normalized['code']);

        $exception = new Exception(code: Response::HTTP_BAD_REQUEST);
        $normalized = $this->normalizer->normalize($exception);
        $this->assertIsArray($normalized);
        $this->assertEquals(expected: Response::HTTP_BAD_REQUEST, actual: $normalized['code']);

        $authenticationMessage = 'Custom authentication exception.';
        $authenticationException = new AuthenticationException(message: $authenticationMessage);
        $exception = new Exception(code: Response::HTTP_BAD_REQUEST, previous: $authenticationException);
        $normalized = $this->normalizer->normalize($exception);
        $this->assertIsArray($normalized);
        $this->assertEquals(expected: Response::HTTP_BAD_REQUEST, actual: $normalized['code']);
        $this->assertEquals(expected: $authenticationMessage, actual: $normalized['message']);
    }
}
