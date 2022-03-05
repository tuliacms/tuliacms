<?php

declare(strict_types=1);

namespace Tulia\Component\Shortcode\Registry;

use ArrayIterator;
use InvalidArgumentException;
use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CompilerRegistry implements CompilerRegistryInterface
{
    protected iterable $compilers = [];

    public function __construct(iterable $compilers = [])
    {
        $this->compilers = $compilers;
    }

    public function add(ShortcodeCompilerInterface $shortcode): void
    {
        $this->compilers[] = $shortcode;
    }

    public function all(): iterable
    {
        return $this->compilers;
    }

    public function replace(iterable $compilers): void
    {
        $this->compilers = $compilers;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->compilers);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->compilers[$offset]);
    }

    public function offsetGet($offset): ShortcodeCompilerInterface
    {
        return $this->compilers[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if (! $value instanceof ShortcodeCompilerInterface) {
            throw new InvalidArgumentException(sprintf('Element must be instance of %s', ShortcodeCompilerInterface::class));
        }

        if ($offset !== null) {
            $this->compilers[$offset] = $value;
        } else {
            $this->compilers[] = $value;
        }
    }

    public function offsetUnset($offset): void
    {
        unset($this->compilers[$offset]);
    }
}
