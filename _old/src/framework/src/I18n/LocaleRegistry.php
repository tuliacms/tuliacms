<?php

declare(strict_types=1);

namespace Tulia\Framework\I18n;

/**
 * @author Adam Banaszkiewicz
 */
class LocaleRegistry implements LocaleRegistryInterface
{
    /**
     * @var array
     */
    protected $locales = [];

    /**
     * @param array $locales
     */
    public function __construct(array $locales)
    {
        foreach ($locales as $locale) {
            $this->add($locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->locales);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->locales[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->locales[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof LocaleInterface) {
            $type = gettype($value);

            if ($type === 'object') {
                $type = \get_class($value);
            }

            throw new \InvalidArgumentException(sprintf('Locale must be an instance of %s, %s given.', LocaleInterface::class, $type));
        }

        if ($offset !== null) {
            $this->locales[$offset] = $value;
        } else {
            $this->locales[] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->locales[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function add(string $code): void
    {
        $this->locales[$code] = new Locale($code);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $code): LocaleInterface
    {
        return $this->locales[$code];
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $code): bool
    {
        return isset($this->locales[$code]);
    }
}
