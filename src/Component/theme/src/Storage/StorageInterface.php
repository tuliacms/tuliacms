<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Storage;

use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface extends \IteratorAggregate
{
    /**
     * @return iterable
     */
    public function all(): iterable;

    /**
     * @param ThemeInterface $theme
     */
    public function add(ThemeInterface $theme): void;

    /**
     * @param string $name
     *
     * @return ThemeInterface
     */
    public function get(string $name): ThemeInterface;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool;
};
