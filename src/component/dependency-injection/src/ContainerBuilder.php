<?php

declare(strict_types=1);

namespace Tulia\Component\DependencyInjection;

use Tulia\Component\DependencyInjection\Exception\MissingDefinitionException;
use Tulia\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContainerBuilder implements ContainerBuilderInterface
{
    use ParametersTrait;

    /**
     * @var array
     */
    protected $definitions = [];

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * @var array
     */
    protected $groups = [];

    /**
     * {@inheritdoc}
     */
    public function getGroup(string $name): ContainerBuilderInterface
    {
        if (isset($this->groups[$name])) {
            return $this->groups[$name];
        }

        return $this->groups[$name] = new self();
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition($id)
    {
        if (! isset($this->definitions[$id])) {
            throw new MissingDefinitionException(sprintf('Definition %s not found.', $id));
        }

        return $this->definitions[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function setDefinition(string $id, string $classname, array $options = []): void
    {
        $options['id'] = $id;
        $options['classname'] = $classname;

        $definition = array_merge([
            'tags'        => [],
            'arguments'   => [],
            'factory'     => null,
            'alias_of'    => null,
            'calls'       => [],
            'pass_tagged' => [],
            'pass_tagged_lazy' => [],
            'debug'       => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[0] ?? [],
        ], $options);

        $definition['tags'] = $this->resolveTags($definition['tags']);

        $this->definitions[$id] = $definition;
    }

    /**
     * {@inheritdoc}
     */
    public function hasDefinition($id): bool
    {
        return isset($this->definitions[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function setAlias(string $alias, string $id): void
    {
        $this->setDefinition($alias, $id, [
            'alias_of' => $id,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaggedDefinitions(string $tag): array
    {
        $definitions = [];

        foreach ($this->definitions as $id => $definition) {
            if (isset($definition['tags'][$tag])) {
                $definitions[$id] = $definition['tags'][$tag];
            }
        }

        return $definitions;
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension(ExtensionInterface $extension): void
    {
        $this->extensions[] = $extension;
    }

    /**
     * {@inheritdoc}
     */
    public function prependExtension(ExtensionInterface $extension): void
    {
        $this->extensions = array_merge([$extension], $this->extensions);
    }

    /**
     * {@inheritdoc}
     */
    public function compile(): ContainerInterface
    {
        foreach ($this->extensions as $extension) {
            $extension->register($this);
        }

        foreach ($this->extensions as $extension) {
            $extension->compile($this);
        }

        $groups = [];

        foreach ($this->groups as $name => $group) {
            $groups[$name] = new Container($group->definitions, $group->parameters);
        }

        $container = new Container($this->definitions, $this->parameters, $groups);

        foreach ($this->extensions as $extension) {
            $extension->build($container);
        }

        return $container;
    }

    /**
     * @param array $tags
     *
     * @return array
     */
    private function resolveTags(array $tags): array
    {
        if ($tags === []) {
            return [];
        }

        $result = [];

        foreach ($tags as $key => $val) {
            if (\is_string($key)) {
                // Tagname in key
                $result[$key][] = $val;
            } elseif (isset($val['name'])) {
                // Tagname in 'name' index
                $name = $val['name'];
                unset($val['name']);

                $result[$name][] = $val;
            } elseif (\is_string($val)) {
                // Tagname as singular value
                $result[$val][] = [];
            }
        }

        return $result;
    }
}
