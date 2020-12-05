<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator;

use GeneratedHydrator\Configuration;
use Zend\Hydrator\HydratorInterface as ZendHydrator;

/**
 * @author Adam Banaszkiewicz
 */
class OcramiusHydrator implements HydratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function hydrate(array $data, $object): object
    {
        if (\is_string($object)) {
            $object = (new \ReflectionClass($object))->newInstanceWithoutConstructor();
        }

        if (\is_object($object) === false) {
            throw new \InvalidArgumentException('Second argument must be and object or fully-qualified classname.');
        }

        $this->getHydrator(\get_class($object))->hydrate($data, $object);

        return $object;
    }

    /**
     * {@inheritdoc}
     */
    public function extract(object $object): array
    {
        return $this->getHydrator(\get_class($object))->extract($object);
    }

    public function getHydrator(string $classname): ZendHydrator
    {
        $hydrator = (new Configuration($classname))
            ->createFactory()
            ->getHydratorClass();

        return new $hydrator();
    }
}
