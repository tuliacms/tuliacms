<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection;

use Tulia\Component\DependencyInjection\Exception\MissingParameterException;

/**
 * @author Adam Banaszkiewicz
 */
trait ParametersTrait
{
    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * {@inheritdoc}
     */
    public function getParameter(string $id)
    {
        if (! isset($this->parameters[$id])) {
            throw new MissingParameterException(sprintf('Parameter %s not found.', $id));
        }

        return $this->parameters[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter(string $id): bool
    {
        return isset($this->parameters[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter(string $id, $value): void
    {
        $this->parameters[$id] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeParameter(string $id, $value): void
    {
        if (isset($this->parameters[$id])) {
            $this->parameters[$id] = array_merge($this->parameters[$id], $value);
        } else {
            $this->parameters[$id] = $value;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
