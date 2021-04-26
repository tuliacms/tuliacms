<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Query;

use Tulia\Cms\Activity\Infrastructure\Persistence\Query\QueryInterface;
use Tulia\Cms\Activity\Query\Model\Row;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Finder implements FinderInterface
{
    /**
     * @var QueryInterface
     */
    private $query;

    /**
     * @var CurrentWebsiteInterface
     */
    private $currentWebsite;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param QueryInterface $query
     * @param CurrentWebsiteInterface $currentWebsite
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        QueryInterface $query,
        CurrentWebsiteInterface $currentWebsite,
        HydratorInterface $hydrator
    ) {
        $this->query = $query;
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

        $source = $this->query->findCollection([
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
            ], Row::class);
        }

        return $result;
    }
}
