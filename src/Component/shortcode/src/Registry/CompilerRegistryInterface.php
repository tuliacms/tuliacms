<?php

declare(strict_types=1);

namespace Tulia\Component\Shortcode\Registry;

use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface CompilerRegistryInterface extends \ArrayAccess, \IteratorAggregate
{
    /**
     * @param ShortcodeCompilerInterface $shortcode
     */
    public function add(ShortcodeCompilerInterface $shortcode): void;

    /**
     * @return iterable
     */
    public function all(): iterable;

    /**
     * @param array $shortcodes
     */
    public function replace(iterable $shortcodes): void;
}
