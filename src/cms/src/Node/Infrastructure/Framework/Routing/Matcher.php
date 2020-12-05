<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Framework\Routing;

use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Node\Query\Enum\ScopeEnum;
use Tulia\Cms\Node\Query\Exception\MultipleFetchException;
use Tulia\Cms\Node\Query\Exception\QueryException;
use Tulia\Cms\Node\Query\Exception\QueryNotFetchedException;
use Tulia\Cms\Node\Query\FinderFactoryInterface;
use Tulia\Cms\Node\Query\Model\Node;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;
use Tulia\Component\Routing\Matcher\MatcherInterface;
use Tulia\Component\Routing\Request\RequestContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Matcher implements MatcherInterface
{
    /**
     * @var FinderFactoryInterface
     */
    protected $finderFactory;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @var FrontendRouteSuffixResolver
     */
    protected $frontendRouteSuffixResolver;

    /**
     * @param FinderFactoryInterface $finderFactory
     * @param RegistryInterface $registry
     */
    public function __construct(
        FinderFactoryInterface $finderFactory,
        RegistryInterface $registry,
        FrontendRouteSuffixResolver $frontendRouteSuffixResolver
    ) {
        $this->finderFactory = $finderFactory;
        $this->registry      = $registry;
        $this->frontendRouteSuffixResolver = $frontendRouteSuffixResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $pathinfo, RequestContextInterface $context): array
    {
        if ($context->isBackend()) {
            return [];
        }

        $pathinfo = $this->frontendRouteSuffixResolver->removeSuffix($pathinfo);

        try {
            /** @var Node $node */
            $node = $this->getNode(substr($pathinfo, 1));
        } catch (\Exception $e) {
            return [];
        }

        if (! $node) {
            return [];
        }

        $nodeType = $this->registry->getType($node->getType());

        if (! $nodeType || $nodeType->isRoutable() === false) {
            return [];
        }

        return [
            'node' => $node,
            'slug' => $node->getSlug(),
            '_route' => 'node_' . $node->getId(),
            '_controller' => $nodeType->getController(),
        ];
    }

    /**
     * @param $slug
     *
     * @return Node|null
     *
     * @throws MultipleFetchException
     * @throws QueryException
     * @throws QueryNotFetchedException
     */
    private function getNode($slug): ?Node
    {
        $finder = $this->finderFactory->getInstance(ScopeEnum::ROUTING_MATCHER);
        $finder->setCriteria([
            'slug'      => $slug,
            'per_page'  => 1,
            'order_by'  => null,
            'order_dir' => null,
        ]);
        $finder->fetch();

        return $finder->getResult()->first();
    }
}
