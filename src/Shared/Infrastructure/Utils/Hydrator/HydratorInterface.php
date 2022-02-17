<?php

declare(strict_types=1);

namespace Tulia\Cms\Shared\Infrastructure\Utils\Hydrator;

/**
 * @author Adam Banaszkiewicz
 */
interface HydratorInterface
{
    /**
     * @param array $data
     * @param string|object $object
     *
     * @return object
     */
    public function hydrate(array $data, $object): object;

    /**
     * @param object $object
     *
     * @return array
     */
    public function extract(object $object): array;
}
