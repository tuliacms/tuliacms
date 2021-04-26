<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Cms\Metadata;

use Tulia\Cms\Metadata\LazyMetadata;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term;

/**
 * @author Adam Banaszkiewicz
 */
class Loader
{
    /**
     * @var SyncerInterface
     */
    protected $syncer;

    /**
     * @param SyncerInterface $syncer
     */
    public function __construct(SyncerInterface $syncer)
    {
        $this->syncer = $syncer;
    }

    public function load(Term $term): void
    {
        $term->setMetadata(LazyMetadata::create($this->syncer, 'term', $term->getId()));
    }
}
