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

    /**
     * {@inheritdoc}
     */
    public function add(ShortcodeCompilerInterface $shortcode): void
    {
        $this->compilers[] = $shortcode;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): iterable
    {
        return $this->compilers;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(iterable $compilers): void
    {
        $this->compilers = $compilers;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->compilers);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset): bool
    {
        return isset($this->compilers[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->compilers[$offset];
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset): void
    {
        unset($this->compilers[$offset]);
    }
}
