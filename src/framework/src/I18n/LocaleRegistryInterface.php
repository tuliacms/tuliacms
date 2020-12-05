<?php

declare(strict_types=1);

namespace Tulia\Framework\I18n;

/**
 * @author Adam Banaszkiewicz
 */
interface LocaleRegistryInterface extends \ArrayAccess, \IteratorAggregate
{
    public function add(string $code): void;
    public function get(string $code): LocaleInterface;
    public function has(string $code): bool;
}
