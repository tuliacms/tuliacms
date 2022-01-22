<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\ContentTypeRoutingStrategy;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\Taxonomy\Ports\Domain\ReadModel\TermFinderInterface;
use Tulia\Cms\Taxonomy\Ports\Infrastructure\Persistence\Domain\ReadModel\TermPathReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SimpleStrategy extends AbstractRoutingStrategy
{
    public function __construct(
        TermPathReadStorageInterface $storage,
        TermFinderInterface $termFinder,
        ContentTypeRegistry $contentTypeRegistry
    ) {
        parent::__construct($storage, $termFinder, $contentTypeRegistry);
    }

    public function generate(string $id, array $parameters = []): string
    {
        return '/' . $parameters['_term_instance']->getSlug();
    }

    public function supports(string $contentType): bool
    {
        return $contentType === 'taxonomy';
    }

    public function getId(): string
    {
        return 'simple';
    }
}
