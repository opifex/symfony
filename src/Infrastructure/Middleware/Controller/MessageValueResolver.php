<?php

declare(strict_types=1);

namespace App\Infrastructure\Middleware\Controller;

use App\Domain\Contract\Message\MessageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MessageValueResolver implements ValueResolverInterface
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
        private NormalizerInterface $normalizer,
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
            $parameters = $this->normalizer->normalize($request);
            $message = $this->denormalizer->denormalize($parameters, $type, context: [
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            ]);
        }

        return isset($message) ? [$message] : [];
    }
}
