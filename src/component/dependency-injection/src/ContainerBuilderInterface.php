<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection;

use Tulia\Component\DependencyInjection\Exception\MissingDefinitionException;
use Tulia\Component\DependencyInjection\Exception\MissingParameterException;
use Tulia\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface ContainerBuilderInterface extends ParametersInterface
{
    /**
     * @param string $name
     *
     * @return ContainerBuilderInterface
     */
    public function getGroup(string $name): ContainerBuilderInterface;

    /**
     * @return array
     */
    public function getGroups(): array;

    /**
     * @param $id
     *
     * @return mixed
     *
     * @throws MissingDefinitionException
     */
    public function getDefinition($id);

    /**
     * @param string $id
     * @param string $classname
     * @param array  $options
     */
    public function setDefinition(string $id, string $classname, array $options = []): void;

    /**
     * @param string $alias
     * @param string $id
     */
    public function setAlias(string $alias, string $id): void;

    /**
     * @param $id
     *
     * @return bool
     */
    public function hasDefinition($id): bool;

    /**
     * @param ExtensionInterface $extension
     */
    public function addExtension(ExtensionInterface $extension): void;

    /**
     * @param ExtensionInterface $extension
     */
    public function prependExtension(ExtensionInterface $extension): void;

    /**
     * @param string $tag
     *
     * @return array
     */
    public function getTaggedDefinitions(string $tag): array;

    /**
     * @return ContainerInterface
     */
    public function compile(): ContainerInterface;
}
