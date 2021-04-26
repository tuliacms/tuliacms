<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection;

/**
 * @author Adam Banaszkiewicz
 */
class ParametersBag implements ParametersInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter(string $id)
    {
        return $this->container->getParameter($id);
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter(string $id): bool
    {
        return $this->container->hasParameter($id);
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter(string $id, $value): void
    {
        $this->container->setParameter($id, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function mergeParameter(string $id, $value): void
    {
        $this->container->mergeParameter($id, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        return $this->container->getParameters();
    }
}
