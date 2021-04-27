<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Storage;

use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface StorageInterface extends \IteratorAggregate
{
    public function all(): iterable;

    public function add(ThemeInterface $theme): void;

    public function get(string $name): ThemeInterface;

    public function has(string $name): bool;
};
