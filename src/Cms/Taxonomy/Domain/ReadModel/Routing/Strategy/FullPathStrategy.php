<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\ReadModel\Routing\Strategy;

use Psr\Log\LoggerInterface;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Finder\TermFinderInterface;
use Tulia\Cms\Taxonomy\Domain\ReadModel\Service\TermPathReadStorageInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class FullPathStrategy extends AbstractRoutingStrategy
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
        $path = '';
        $term = $this->storage->findTermToPathGeneration($id, $parameters['_locale']);

        while ($term !== null) {
            $path = "/{$term['slug']}" . $path;

            if ($term['parent_id'] && $term['parent_id'] !== Term::ROOT_ID) {
                $term = $this->storage->findTermToPathGeneration($term['parent_id'], $parameters['_locale']);
            } else {
                break;
            }
        }

        return $path;
    }

    public function supports(string $contentType): bool
    {
        return $contentType === 'taxonomy';
    }

    public function getId(): string
    {
        return 'full_path';
    }
}
