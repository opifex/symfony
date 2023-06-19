<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware;

use App\Domain\Contract\MessageInterface;
use App\Domain\Exception\ExtraAttributesHttpException;
use App\Domain\Exception\NormalizationFailedHttpException;
use App\Domain\Exception\ValidationFailedHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ControllerMessageValueResolver implements ValueResolverInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private NormalizerInterface $normalizer,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @return MessageInterface[]
     * @throws ExceptionInterface
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();

        if (is_string($type) && is_subclass_of($type, class: MessageInterface::class)) {
            $context = [AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false];
            $parameters = $this->normalizer->normalize($request);

            try {
                $message = $this->denormalizer->denormalize($parameters, $type, context: $context);
            } catch (ExtraAttributesException $e) {
                throw new ExtraAttributesHttpException($e->getExtraAttributes(), $type);
            } catch (NotNormalizableValueException $e) {
                throw new NormalizationFailedHttpException($e->getExpectedTypes(), $e->getPath(), $type);
            }

            $violations = $this->validator->validate($message);

            if ($violations->count()) {
                throw new ValidationFailedHttpException($violations);
            }
        }

        return isset($message) ? [$message] : [];
    }
}
