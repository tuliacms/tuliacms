<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\Router;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Model\ContentType;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TermPathReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SymfonyRouter implements RouterInterface, RequestMatcherInterface
{
    private TermPathReadStorageInterface $storage;

    private FrontendRouteSuffixResolver $frontendRouteSuffixResolver;

    private TermFinderInterface $termFinder;

    private ContentTypeRegistryInterface $contentTypeRegistry;

    private Router $contentTypeRouter;

    private ?RequestContext $context = null;

    public function __construct(
        TermPathReadStorageInterface $storage,
        FrontendRouteSuffixResolver $frontendRouteSuffixResolver,
        TermFinderInterface $termFinder,
        ContentTypeRegistryInterface $contentTypeRegistry,
        Router $contentTypeRouter
    ) {
        $this->storage = $storage;
        $this->frontendRouteSuffixResolver = $frontendRouteSuffixResolver;
        $this->termFinder = $termFinder;
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->contentTypeRouter = $contentTypeRouter;
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

    /**
     * {@inheritdoc}
     */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): ?string
    {
        if (strncmp($name, 'term.', 5) !== 0) {
            return '';
        }

        // @todo Fix routing locales
        $locale = 'en_US';//$this->getContext()->getParameter('_content_locale'),
        [, $type, $identity] = explode('.', $name);

        $path = $this->storage->findPathByTermId($identity, $locale)['path'] ?? null;

        return $path ? $this->frontendRouteSuffixResolver->appendSuffix($path) : '';
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
        // @todo Fix routing locales
        $locale = 'en_US';//$this->getContext()->getParameter('_content_locale'),
        $termId = $this->storage->findTermIdByPath($pathinfo, $locale);

        if ($termId === null) {
            throw new ResourceNotFoundException('Term not exists for given path.');
        }

        /** @var Term $term */
        $term = $this->getTerm($termId);

        if ($this->isTermRoutable($term, $termType) === false) {
            throw new ResourceNotFoundException('Taxonomy type not exists or is not routable.');
        }

        return [
            'term' => $term,
            'slug' => $term->getSlug(),
            '_route' => sprintf('term.%s.%s', $term->getType(), $term->getId()),
            '_controller' => $termType->getController(),
        ];
    }

    private function isTermRoutable(?Term $term, ?ContentType &$contentType): bool
    {
        if (! $term instanceof Term) {
            return false;
        }

        $contentType = $this->contentTypeRegistry->get($term->getType());

        return $contentType && $contentType->isRoutable();
    }

    private function getTerm(string $id): ?Term
    {
        return $this->termFinder->findOne([
            'id'            => $id,
            'per_page'      => 1,
            'order_by'      => null,
            'order_dir'     => null,
            'visibility'    => 1,
            'taxonomy_type' => null,
        ], TermFinderScopeEnum::ROUTING_MATCHER);
    }
}
