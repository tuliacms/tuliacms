<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Infrastructure\Cms\Metadata;

use Tulia\Cms\Metadata\LazyMetadata;
use Tulia\Cms\Metadata\Syncer\SyncerInterface;
use Tulia\Cms\User\Query\Model\User;

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

    public function load(User $user): void
    {
        $user->setMetadata(LazyMetadata::create($this->syncer, 'user', $user->getId()));
    }
}
