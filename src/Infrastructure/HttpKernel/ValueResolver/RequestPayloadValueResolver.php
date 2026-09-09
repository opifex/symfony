<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpKernel\ValueResolver;

use App\Infrastructure\HttpKernel\Exception\RequestExtraParamsException;
use App\Infrastructure\HttpKernel\Exception\RequestParamTypeException;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[AsTargetedValueResolver('payload')]
final readonly class RequestPayloadValueResolver implements ValueResolverInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
    ) {
    }

    /**
     * @return object[]
     * @throws ExceptionInterface
     */
    #[Override]
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $payload = (array) $request->attributes->get(key: '_payload');
        $context = [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false];
        $type = $argument->getType() ?? '';

        try {
            /** @var object[] */
            return [$this->denormalizer->denormalize($payload, $type, context: $context)];
        } catch (ExtraAttributesException $exception) {
            throw RequestExtraParamsException::create($exception->getExtraAttributes(), $type);
        } catch (NotNormalizableValueException $exception) {
            throw RequestParamTypeException::create($exception->getExpectedTypes(), $exception->getPath(), $type);
        }
    }
}
