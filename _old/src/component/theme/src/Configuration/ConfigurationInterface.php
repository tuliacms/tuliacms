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
     * $this->add('theme-color', 'red', [ 'hash' => '#ff0000' ]);
     *
     * @param string $group  Group name.
     * @param string $id     Identificator of value(s) in the group.
     * @param array  $values Values to add.
     */
    //public function add(string $group, string $id, array $values);

    /**
     * Return all values from given $group. The method returns all defined values from given $group.
     * Always returns array, even if the values were not set.
     *
     * @param  string $group Group name.
     * @return array
     */
    //public function all(string $group): array;

    /**
     * Return values identified by $id from given $group. The method returns only one values array.
     * Always returns array, even if the values were not set.
     *
     * @param  string $group Group name.
     * @param  string $id    Identificator of value(s) in the group.
     * @return array
     */
    //public function get(string $group, string $id): array;

    /**
     * Return one value, from $values array identified by $id from given $group. If value does not exists
     * or key from value does not exists - returns value of $default.
     *
     * @param  string $group    Group name.
     * @param  string $id       Identificator of value(s) in the group.
     * @param  string $valueKey Key name, from $values array.
     * @param  mixed  $default  Default value, returned if $valueKey does not exists.
     * @return mixed
     */
    //public function getSingle(string $group, string $id, string $valueKey, $default = null);

    //public function has(string $group, ?string $id = null, ?string $valueKey = null): bool;
}
