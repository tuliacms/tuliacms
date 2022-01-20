<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Tulia\Cms\Taxonomy\Ports\Domain\ReadModel\TermFinderInterface;
use Tulia\Cms\Taxonomy\Ports\Domain\ReadModel\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\TermPathReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Router implements RouterInterface, RequestMatcherInterface
{
    private TermPathReadStorageInterface $storage;

    private FrontendRouteSuffixResolver $frontendRouteSuffixResolver;

    private TermFinderInterface $termFinder;

    private ContentTypeRegistry $contentTypeRegistry;

    private ?RequestContext $context = null;

    public function __construct(
        TermPathReadStorageInterface $storage,
        FrontendRouteSuffixResolver $frontendRouteSuffixResolver,
        TermFinderInterface $termFinder,
        ContentTypeRegistry $contentTypeRegistry
    ) {
        $this->storage = $storage;
        $this->frontendRouteSuffixResolver = $frontendRouteSuffixResolver;
        $this->termFinder = $termFinder;
        $this->contentTypeRegistry = $contentTypeRegistry;
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
        if (strncmp($name, 'term_', 5) !== 0) {
            return '';
        }

        // @todo Fix routing locales
        $locale = 'en_US';//$context->getContentLocale();

        $path = $this->storage->find(substr($name, 5), $locale)['path'] ?? null;

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
        // @todo Fix routing locales
        $locale = 'en_US';//$context->getContentLocale()
        $pathinfo = $this->frontendRouteSuffixResolver->removeSuffix($pathinfo);
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
            '_route' => 'term_' . $term->getId(),
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
