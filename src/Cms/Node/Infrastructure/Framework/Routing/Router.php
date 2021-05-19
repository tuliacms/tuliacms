<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Framework\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Model\Node;
use Tulia\Cms\Node\Domain\NodeType\RegistryInterface;
use Tulia\Cms\Node\Domain\ReadModel\Finder\Enum\NodeFinderScopeEnum;
use Tulia\Cms\Node\Ports\Infrastructure\Persistence\Domain\ReadModel\NodeFinderInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;

/**
 * @author Adam Banaszkiewicz
 */
class Router implements RouterInterface, RequestMatcherInterface
{
    private NodeFinderInterface $nodeFinder;

    private RegistryInterface $registry;

    private FrontendRouteSuffixResolver $frontendRouteSuffixResolver;

    private ?RequestContext $context = null;

    public function __construct(
        NodeFinderInterface $nodeFinder,
        RegistryInterface $registry,
        FrontendRouteSuffixResolver $frontendRouteSuffixResolver
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->registry = $registry;
        $this->frontendRouteSuffixResolver = $frontendRouteSuffixResolver;
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function getRouteCollection(): RouteCollection
    {
        // Dynamic routing don't have any static collection
        return new RouteCollection();
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): ?string
    {
        if (strncmp($name, 'node_', 5) !== 0) {
            return null;
        }

        $identity = substr($name, 5);

        $parameters = array_merge([
            // @todo Fix routing locales
            '_locale' => 'pl_PL',//$this->getContext()->getParameter('_content_locale'),
        ], $parameters);

        try {
            $node = $this->getNodeForGenerate($identity, $parameters['_locale']);

            if (! $node) {
                return null;
            }
        } catch (\Exception $e) {
            return null;
        }

        return $this->frontendRouteSuffixResolver->appendSuffix("/{$node->getSlug()}");
    }

    public function matchRequest(Request $request): array
    {
        return $this->match($request->attributes->get('_content_path', $request->getPathInfo()));
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $pathinfo): array
    {
        $pathinfo = urldecode($pathinfo);
        $pathinfo = $this->frontendRouteSuffixResolver->removeSuffix($pathinfo);

        try {
            /** @var Node $node */
            $node = $this->getNode(substr($pathinfo, 1));
        } catch (\Exception $e) {
            throw new ResourceNotFoundException('Node not found with given path.');
        }

        if (! $node) {
            throw new ResourceNotFoundException('Node not found with given path.');
        }

        $nodeType = $this->registry->getType($node->getType());

        if (! $nodeType || $nodeType->isRoutable() === false) {
            throw new ResourceNotFoundException('Node type not exists or is not routable.');
        }

        return [
            'node' => $node,
            'slug' => $node->getSlug(),
            '_route' => 'node_' . $node->getId(),
            '_controller' => $nodeType->getController(),
        ];
    }

    private function getNode(?string $slug): ?Node
    {
        return $this->nodeFinder->findOne([
            'slug'      => $slug,
            'per_page'  => 1,
            'order_by'  => null,
            'order_dir' => null,
        ], NodeFinderScopeEnum::ROUTING_MATCHER);
    }

    private function getNodeForGenerate($identity, string $locale): ?Node
    {
        if ($identity instanceof Node) {
            if ($identity->getLocale() === $locale) {
                return $identity;
            }

            $identity = $identity->getId();
        }

        return $this->nodeFinder->findOne([
            'locale' => $locale,
            'id' => $identity,
        ], NodeFinderScopeEnum::ROUTING_GENERATOR);
    }
}
