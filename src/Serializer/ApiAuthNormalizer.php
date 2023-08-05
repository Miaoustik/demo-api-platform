<?php

namespace App\Serializer;

use App\Attribute\ApiAuthGroups;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class ApiAuthNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    private const ALREADY_CALLED = "PostApiNormalizerAlreadyCalled";

    use NormalizerAwareTrait;

    public function __construct(
        private readonly AuthorizationCheckerInterface $checker
    )
    {
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if (!is_object($data)) return false;

        $alreadyCalled = $context[self::ALREADY_CALLED] ?? false;
        $class = new \ReflectionClass(get_class($data));
        $classAttributes = $class->getAttributes(ApiAuthGroups::class);
        return !empty($classAttributes) && $alreadyCalled === false;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        $class = new \ReflectionClass(get_class($object));
        $apiAuthGroups = $class->getAttributes(ApiAuthGroups::class)[0]->newInstance();

        foreach ($apiAuthGroups as $role => $groups) {
            if ($this->checker->isGranted($role, $object)) {
                $context['groups'] = array_merge($context['groups'] ?? [], $groups);
            }
        }

        return $this->normalizer->normalize($object, $format, $context);
    }
}