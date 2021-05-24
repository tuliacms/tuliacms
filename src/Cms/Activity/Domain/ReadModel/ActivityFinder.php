<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Domain\ReadModel;

use Tulia\Cms\Activity\Domain\ReadModel\Model\ActivityRow;
use Tulia\Cms\Activity\Ports\Infrastructure\Persistence\Domain\ReadModel\ActivityReadStorageInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ActivityFinder
{
    private ActivityReadStorageInterface $storage;

    private CurrentWebsiteInterface $currentWebsite;

    private HydratorInterface $hydrator;

    public function __construct(
        ActivityReadStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        HydratorInterface $hydrator
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->hydrator = $hydrator;
    }

    public function findPart(int $part = 1): array
    {
        $limit = 10;

        if ($part <= 1) {
            $start = 0;
        } else {
            $start = ($part - 1) * $limit;
        }

        $source = $this->storage->findCollection([
            'website_id' => $this->currentWebsite->getId(),
        ], $start, $limit);
        $result = [];

        foreach ($source as $row) {
            $result[] = $this->hydrator->hydrate([
                'id' => $row['id'],
                'websiteId' => $row['website_id'],
                'message' => $row['message'],
                'context' => json_decode($row['context'], true),
                'translationDomain' => $row['translation_domain'],
                'createdAt' => new \DateTime($row['created_at']),
            ], ActivityRow::class);
        }

        return $result;
    }
}
