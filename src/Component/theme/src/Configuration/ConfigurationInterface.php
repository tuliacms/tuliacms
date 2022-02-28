<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Configuration;

/**
 * @author Adam Banaszkiewicz
 */
interface ConfigurationInterface
{
    /**
     * Add new configuration $values, identified by $id to given $group. In example You can add
     * red color hash into theme-color group:
     * $this->add('color', 'red', [ 'hash' => '#ff0000' ]);
     * $this->add('widgrt_space', 'mainmenu', 'Main menu');
     */
    public function add(string $group, string $code, $value = null);

    /**
     * Return all values from given $group. The method returns all defined values from given $group.
     * Always returns array, even if the values were not set.
     */
    public function all(?string $group = null): array;

    /**
     * Return values identified by $id from given $group. The method returns only one values array.
     * Always returns array, even if the values were not set.
     */
    public function get(string $group, string $code, $default = null);

    public function remove(string $group, ?string $code = null): void;

    public function has(string $group, ?string $code = null, ?string $valueKey = null): bool;

    public function getRegisteredWidgetSpaces(): array;

    public function getRegisteredWidgetStyles(): array;

    public function merge(ConfigurationInterface $configuration): void;
}
