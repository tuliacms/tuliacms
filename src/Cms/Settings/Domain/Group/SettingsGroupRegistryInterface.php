<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\Group;

/**
 * @author Adam Banaszkiewicz
 */
interface SettingsGroupRegistryInterface
{
    /**
     * @return iterable|array|SettingsGroupInterface[]
     */
    public function all(): iterable;

    /**
     * @param string $id
     *
     * @return SettingsGroupInterface
     */
    public function getGroup(string $id): SettingsGroupInterface;
}
