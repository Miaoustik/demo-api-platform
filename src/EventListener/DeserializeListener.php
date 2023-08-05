<?php

namespace App\EventListener;

use ApiPlatform\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Symfony\EventListener\DeserializeListener as DecoratedListener;
use ApiPlatform\Util\RequestAttributesExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DeserializeListener
{
    public function __construct(
        private readonly DenormalizerInterface $denormalizer,
        private readonly SerializerContextBuilderInterface $serializerContextBuilder,
        private readonly DecoratedListener $decorated
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void {
        $request = $event->getRequest();
        if ($request->isMethodCacheable(false) || $request->isMethod(Request::METHOD_DELETE)) {
            return;
        }

        if ('form' === $request->getContentType()) {
            $this->denormalizeFormRequest($request);
        } else {
            $this->decorated->onKernelRequest($event);
        }
    }

    private function denormalizeFormRequest(Request $request): void
    {
        $attributes = RequestAttributesExtractor::extractAttributes($request);

        if (empty($attributes)) return;

        $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);
        $data = $request->request->all();
        $files = $request->files->all();

        $populated = $request->attributes->get('data');
        if (null !== $populated) {
            $context['object_to_populate'] = $populated;
        }

        $object = $this->denormalizer->denormalize(
            array_merge($data, $files),
            $attributes['resource_class'],
            null,
            $context
        );

        $request->attributes->set('data', $object);

    }
}