<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Metadata;

use Tulia\Cms\Metadata\LazyMetadata;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\Node\Query\Model\Node;

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

    public function load(Node $node): void
    {
        $node->setMetadata(LazyMetadata::create($this->syncer, 'node', $node->getId()));
    }
}
