<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Attribute\MapMessage;
use App\Domain\Exception\MessageExtraParamsException;
use App\Domain\Exception\MessageParamTypeException;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class MessageValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly DenormalizerInterface $denormalizer,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @return object[]
     * @throws ExceptionInterface
     */
    #[Override]
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attribute = $argument->getAttributesOfType(name: MapMessage::class)[0] ?? null;
        $message = null;

        if ($attribute instanceof MapMessage) {
            $params = (array) $this->normalizer->normalize($request);
            $context = [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false];
            $type = $argument->getType() ?? '';

            try {
                $message = $this->denormalizer->denormalize($params, $type, context: $context);
            } catch (ExtraAttributesException $e) {
                throw MessageExtraParamsException::create($e->getExtraAttributes(), $type);
            } catch (NotNormalizableValueException $e) {
                throw MessageParamTypeException::create($e->getExpectedTypes(), $e->getPath(), $type);
            }
        }

        return $message !== null ? [$message] : [];
    }
}
