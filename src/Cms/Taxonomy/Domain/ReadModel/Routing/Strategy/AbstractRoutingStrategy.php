<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Routing\Strategy;

use Psr\Log\LoggerInterface;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Routing\Strategy\ContentTypeRoutingStrategyInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderScopeEnum;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Model\Term;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TermPathReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractRoutingStrategy implements ContentTypeRoutingStrategyInterface
{
    protected TermPathReadStorageInterface $storage;

    protected TermFinderInterface $termFinder;

    protected ContentTypeRegistry $contentTypeRegistry;

    protected LoggerInterface $logger;

    public function __construct(
        TermPathReadStorageInterface $storage,
        TermFinderInterface $termFinder,
        ContentTypeRegistry $contentTypeRegistry,
        LoggerInterface $logger
    ) {
        $this->storage = $storage;
        $this->termFinder = $termFinder;
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->logger = $logger;
    }

    /**
     * Generate method must be implemented individually by all Routing Strategies, and must not
     * gets data from the flat table (#__term_path). Paths generated by this method goes directly
     * to flat table when new term is created or other updated.
     */
    abstract public function generate(string $id, array $parameters = []): string;

    /**
     * Matching is done by fetching data from flat cached view table (#__term_path).
     * Data goes there when term is saved. Generation is done by the RoutingStrategy::generate()
     * method. So:
     * - Mathing is done the same by all Routing Strategies
     * - Generating is done individually by each Routing Strategy.
     */
    public function match(string $pathinfo, array $parameters = []): array
    {
        $termId = $this->storage->findTermIdByPath($pathinfo, $parameters['_locale']);

        if ($termId === null) {
            return [];
        }

        /** @var Term $term */
        $term = $this->getTerm($termId);

        if ($this->isTermRoutable($term, $termType) === false) {
            $this->logger->info('Taxonomy type not exists or is not routable.');
            return [];
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
