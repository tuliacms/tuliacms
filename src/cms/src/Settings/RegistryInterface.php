<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings;

/**
 * @author Adam Banaszkiewicz
 */
interface RegistryInterface
{
    /**
     * @return iterable|array|GroupInterface[]
     */
    public function all(): iterable;

    /**
     * @param string $id
     *
     * @return GroupInterface
     */
    public function getGroup(string $id): GroupInterface;
}
