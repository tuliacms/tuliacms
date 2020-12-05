<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing;

use Tulia\Cms\Platform\Infrastructure\Framework\Routing\FrontendRouteSuffixResolver;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\TaxonomyTypeInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\TermPath\StorageInterface;
use Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Component\Routing\Matcher\MatcherInterface;
use Tulia\Component\Routing\Request\RequestContextInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Matcher implements MatcherInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

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
     * @param StorageInterface $storage
     * @param FinderFactoryInterface $finderFactory
     * @param RegistryInterface $registry
     * @param FrontendRouteSuffixResolver $frontendRouteSuffixResolver
     */
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

    /**
     * {@inheritdoc}
     */
    public function match(string $pathinfo, RequestContextInterface $context): array
    {
        if ($context->isBackend()) {
            return [];
        }

        $pathinfo = $this->frontendRouteSuffixResolver->removeSuffix($pathinfo);
        $termId = $this->storage->findByPath($pathinfo, $context->getContentLocale());

        if ($termId === null) {
            return [];
        }

        /** @var Term $term */
        $term = $this->getTerm($termId);

        if ($this->isTermRoutable($term, $termType) === false) {
            return [];
        }

        return [
            'term' => $term,
            'slug' => $term->getSlug(),
            '_route' => 'term_' . $term->getId(),
            '_controller' => $termType->getController(),
        ];
    }

    /**
     * @param Term|null $term
     * @param null|TaxonomyTypeInterface $termType
     *
     * @return bool
     */
    private function isTermRoutable(?Term $term, &$termType): bool
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
