<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Attribute\MapMessage;
use App\Domain\Exception\MessageExtraParamsException;
use App\Domain\Exception\MessageNormalizationException;
use App\Domain\Exception\MessageParamTypeException;
use App\Domain\Exception\ValidationFailedException;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class MessageValueResolver implements ValueResolverInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private NormalizerInterface $normalizer,
        private ValidatorInterface $validator,
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
        $messageValue = null;

        if ($attribute instanceof MapMessage) {
            $messageType = $argument->getType() ?? '';
            $messageParams = $this->extractParams($request);
            $messageValue = $this->buildMessage($messageParams, $messageType);

            $violations = $this->validator->validate($messageValue);

            if ($violations->count()) {
                throw new ValidationFailedException($violations);
            }
        }

        return $messageValue !== null ? [$messageValue] : [];
    }

    /**
     * @param Request $request
     * @return array&array<string, mixed>
     * @throws ExceptionInterface
     */
    private function extractParams(Request $request): array
    {
        try {
            return (array) $this->normalizer->normalize($request);
        } catch (LogicException $e) {
            throw new MessageNormalizationException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param array&array<string, mixed> $params
     * @param string $type
     * @return object
     * @throws ExceptionInterface
     */
    private function buildMessage(array $params, string $type): object
    {
        $context = [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false];

        try {
            return $this->denormalizer->denormalize($params, $type, context: $context);
        } catch (ExtraAttributesException $e) {
            throw new MessageExtraParamsException($e->getExtraAttributes(), $type);
        } catch (NotNormalizableValueException $e) {
            throw new MessageParamTypeException($e->getExpectedTypes(), $e->getPath(), $type);
        }
    }
}
