<?php

namespace App\Serializer;

use App\Entity\HasFileInterface;
use ArrayObject;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Vich\UploaderBundle\Storage\StorageInterface;

class ApiFileNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'ApiFileNormalizerAlreadyCalled';

    public function __construct(private readonly StorageInterface $storage)
    {
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if (!is_object($data)) return false;
        $alreadyCalled = $context[self::ALREADY_CALLED] ?? false;
        $class = new \ReflectionClass(get_class($data));
        return $alreadyCalled === false && $class->implementsInterface(HasFileInterface::class);
    }

    /**
     * @param HasFileInterface $object
     * @param string|null $format
     * @param array $context
     * @return array|ArrayObject|bool|float|int|string|null
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        $object->setFileUrl($this->storage->resolveUri($object, 'file'));

        return $this->normalizer->normalize($object, $format, $context);
    }
}