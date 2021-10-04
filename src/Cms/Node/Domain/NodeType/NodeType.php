<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\NodeType;

/**
 * @author Adam Banaszkiewicz
 */
class NodeType implements NodeTypeInterface
{
    protected $type = 'page';
    protected $statuses = [ 'trashed', 'published', 'sketch' ];

    /**
     * Predefined CMS's supports:
     * - content      - Node content (text edited by some editor, ie. WYSIWYG).
     * - introduction - Node introduction (text edited by textarea).
     * - tags         - Add tags to node.
     * - quick-create - Creating node in Menus or quick add form mode.
     * - searchable   - Allowing search this type of nodes in search engine.
     * - hierarchy    - This node type is allowed to live in hierarchy.
     *
     * @var array
     */
    protected $supports = [ 'content' ];

    /**
     * Defines if this node type has own web pages. If no, nodes will only
     * be available on taxonomies pages, links to this nodes will not work.
     *
     * @var bool
     */
    protected $isRoutable = true;

    /**
     * This taxonomy will be treated as main category of node, and will
     * be used to create node's path (URL) if selected routing strategy
     * required this.
     *
     * @var string
     */
    protected $routableTaxonomy;
    protected $controller = NodeTypeInterface::CONTROLLER;
    protected $translationDomain = 'pages';
    protected $taxonomies = [];
    protected $parameters = [];

    /**
     * @param string $type
     */
    public function __construct(string $type = 'page')
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getStatuses(): array
    {
        return $this->statuses;
    }

    public function setStatuses(array $statuses): void
    {
        $this->statuses = $statuses;
    }

    public function addStatus($status): void
    {
        $this->statuses = array_merge($this->statuses, \is_array($status) ? $status : [ $status ]);
    }

    public function removeStatus(string $status): void
    {
        $key = array_search($status, $this->statuses);

        if ($key !== false) {
            unset($this->statuses[$key]);
            $this->statuses = array_values($this->statuses);
        }
    }

    public function hasStatus(string $status): bool
    {
        return \in_array($status, $this->statuses, true);
    }

    public function getIsRoutable(): bool
    {
        return $this->isRoutable;
    }

    public function isRoutable(): bool
    {
        return $this->isRoutable;
    }

    public function setIsRoutable(bool $isRoutable): void
    {
        $this->isRoutable = $isRoutable;
    }

    public function getRoutableTaxonomy(): string
    {
        return $this->routableTaxonomy;
    }

    public function setRoutableTaxonomy(string $routableTaxonomy): void
    {
        $this->routableTaxonomy = $routableTaxonomy;
    }

    public function getController(): ?string
    {
        return $this->controller;
    }

    public function setController(?string $controller): void
    {
        $this->controller = $controller;
    }

    public function supports(string $name): bool
    {
        return \in_array($name, $this->supports, true);
    }

    public function getSupports(): array
    {
        return $this->supports;
    }

    public function setSupports(array $supports): void
    {
        $this->supports = $supports;
    }

    public function addSupport($support): void
    {
        $this->supports = array_merge($this->supports, \is_array($support) ? $support : [ $support ]);
    }

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

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    public function getTaxonomies(): array
    {
        return $this->taxonomies;
    }

    public function hasTaxonomy(string $name): bool
    {
        foreach ($this->taxonomies as $taxonomy) {
            if ($taxonomy['taxonomy'] === $name) {
                return true;
            }
        }

        return false;
    }

    public function setTaxonomies(array $taxonomies): void
    {
        foreach ($taxonomies as $taxonomy) {
            if (isset($taxonomy['taxonomy'])) {
                $this->addTaxonomy($taxonomy['taxonomy'], $taxonomy['params'] ?? []);
            }
        }
    }

    public function addTaxonomy(string $taxonomy, array $params = []): void
    {
        $this->taxonomies[] = [
            'taxonomy' => $taxonomy,
            'params'   => array_merge([
                'multiple' => true,
            ], $params)
        ];
    }

    public function removeTaxonomy(string $name): void
    {
        foreach ($this->taxonomies as $key => $taxonomy) {
            if ($taxonomy['taxonomy'] === $name) {
                unset($this->taxonomies[$key]);
                $this->taxonomies = array_values($this->taxonomies);
            }
        }
    }

    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    public function getParameter(string $name, $default = null)
    {
        return $this->parameters[$name] ?? $default;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function setParameters(array $parameters): void
    {
        $this->parameters[] = $parameters;
    }

    public function mergeParameters(array $parameters): void
    {
        $this->parameters = array_merge($this->parameters, $parameters);
    }
}
