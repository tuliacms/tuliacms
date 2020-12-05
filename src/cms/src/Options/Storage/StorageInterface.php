<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Storage;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface
{
    /**
     * @param string $name
     * @param null $default
     *
     * @return mixed
     */
    public function get(string $name, $default = null);

    /**
     * @param string $name
     * @param $value
     */
    public function set(string $name, $value): void;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;

    /**
     * @param string $name
     */
    public function remove(string $name): void;

    /**
     * @param string $old
     * @param string $new
     */
    public function rename(string $old, string $new): void;

    /**
     * @param string $name
     * @param $value
     * @param bool $multilingual
     * @param bool|null $autoload
     */
    public function create(string $name, $value, bool $multilingual = false, bool $autoload = null): void;
}
