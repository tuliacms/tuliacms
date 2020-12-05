<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\TaxonomyType;

use Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy\SimpleStrategy;

/**
 * @author Adam Banaszkiewicz
 */
class TaxonomyType implements TaxonomyTypeInterface
{
    protected $type = 'category';

    /**
     * Predefined CMS's supports:
     * - content      - Taxonomy content (text edited by some editor, ie. WYSIWYG).
     * - thumbnail    - Taxonomy thumbnail (ID of the image).
     * - quick-create - Creating node in Menus or quick add form mode.
     * - hierarchy    - This node type is allowed to live in hierarchy.
     *
     * @var array
     */
    protected $supports = [];

    protected $isRoutable = true;

    /**
     * Name of one of the \Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy\StrategyInterface
     *
     * @var string
     */
    protected $routingStrategy = SimpleStrategy::NAME;
    protected $controller = TaxonomyTypeInterface::CONTROLLER;
    protected $translationDomain = 'pages';
    protected $parameters = [];

    /**
     * @param string $type
     */
    public function __construct(string $type = 'page')
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(): ?string
    {
        return $this->controller;
    }

    /**
     * {@inheritdoc}
     */
    public function setController(?string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsRoutable(): bool
    {
        return $this->isRoutable;
    }

    /**
     * {@inheritdoc}
     */
    public function isRoutable(): bool
    {
        return $this->isRoutable;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRoutable(bool $isRoutable): void
    {
        $this->isRoutable = $isRoutable;
    }

    /**
     * {@inheritdoc}
     */
    public function setRoutingStrategy(string $name): void
    {
        $this->routingStrategy = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoutingStrategy(): string
    {
        return $this->routingStrategy;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(string $name): bool
    {
        return \in_array($name, $this->supports, true);
    }

    /**
     * {@inheritdoc}
     */
    public function getSupports(): array
    {
        return $this->supports;
    }

    /**
     * {@inheritdoc}
     */
    public function setSupports(array $supports): void
    {
        $this->supports = $supports;
    }

    /**
     * {@inheritdoc}
     */
    public function addSupport($support): void
    {
        $this->supports = array_merge($this->supports, \is_array($support) ? $support : [ $support ]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeSupport($support): void
    {
        $support = \is_array($support) ? $support : [ $support ];

        foreach ($this->supports as $key => $name) {
            if (\in_array($name, $support, true)) {
                unset($this->supports[$key]);
                $this->supports = array_values($this->supports);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter(string $name, $default = null)
    {
        return $this->parameters[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters[] = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function mergeParameters(array $parameters): void
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }
}
