<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\TermPath\StorageInterface;
use Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class Router implements RouterInterface, RequestMatcherInterface
{
    protected StorageInterface $storage;
    protected FinderFactoryInterface $finderFactory;
    protected RegistryInterface $registry;
    protected FrontendRouteSuffixResolver $frontendRouteSuffixResolver;
    protected ?RequestContext $context = null;

    public function __construct(
        StorageInterface $storage,
        FinderFactoryInterface $finderFactory,
        RegistryInterface $registry,
        FrontendRouteSuffixResolver $frontendRouteSuffixResolver
    ) {
        $this->storage       = $storage;
        $this->finderFactory = $finderFactory;
        $this->registry      = $registry;
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

    /**
     * {@inheritdoc}
     */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): ?string
    {
        if (strncmp($name, 'term_', 5) !== 0) {
            return '';
        }

        // @todo Fix routing locales
        $locale = 'pl_PL';//$context->getContentLocale();

        $path = $this->storage->find(substr($name, 5), $locale)['path'] ?? null;

        return $path ? $this->frontendRouteSuffixResolver->appendSuffix($path) : $path;
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
        $locale = 'pl_PL';//$context->getContentLocale()
        $pathinfo = $this->frontendRouteSuffixResolver->removeSuffix($pathinfo);
        $termId = $this->storage->findByPath($pathinfo, $locale);

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

    private function isTermRoutable(?Term $term, ?TaxonomyTypeInterface &$termType): bool
    {
        if (! $term instanceof Term) {
            return false;
        }

        $termType = $this->registry->getType($term->getType());

        return $termType && $termType->isRoutable();
    }

    /**
     * @param string $id
     *
     * @return Term|null
     */
    private function getTerm(string $id): ?Term
    {
        try {
            $finder = $this->finderFactory->getInstance(ScopeEnum::ROUTING_MATCHER);
            $finder->setCriteria([
                'id'            => $id,
                'per_page'      => 1,
                'order_by'      => null,
                'order_dir'     => null,
                'visibility'    => 1,
                'taxonomy_type' => null,
            ]);
            $finder->fetchRaw();

            return $finder->getResult()->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}
