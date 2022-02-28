<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\Group;

/**
 * @author Adam Banaszkiewicz
 */
interface SettingsGroupRegistryInterface
{
    /**
     * @return SettingsGroupInterface[]
     */
    public function all(): iterable;

    public function getGroup(string $id): SettingsGroupInterface;
}
