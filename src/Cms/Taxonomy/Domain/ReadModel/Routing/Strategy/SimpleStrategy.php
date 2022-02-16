<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Routing\Strategy;

use Psr\Log\LoggerInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistry;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TermPathReadStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SimpleStrategy extends AbstractRoutingStrategy
{
    public function __construct(
        TermPathReadStorageInterface $storage,
        TermFinderInterface $termFinder,
        ContentTypeRegistry $contentTypeRegistry,
        LoggerInterface $logger
    ) {
        parent::__construct($storage, $termFinder, $contentTypeRegistry, $logger);
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
