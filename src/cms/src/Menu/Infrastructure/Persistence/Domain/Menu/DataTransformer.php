<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\Menu;

use Tulia\Cms\Menu\Domain\Menu\Model\Aggregate\Menu as Aggregate;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\AggregateId;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DataTransformer
{
    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @param HydratorInterface $hydrator
     */
    public function __construct(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }

    public function arrayToAggregate(array $item): Aggregate
    {
        /** @var Aggregate $aggregate */
        $aggregate = $this->hydrator->hydrate([
            'id'        => new AggregateId($item['id']),
            'websiteId' => $item['website_id'],
            'name'      => $item['name'],
            'items'     => $item['items'],
        ], Aggregate::class);

        return $aggregate;
    }

    public function aggregateToInsert(Aggregate $aggregate): array
    {
        $data = $this->hydrator->extract($aggregate);

        $mainTable = [];

        $mainTable['id']         = $data['id']->getId();
        $mainTable['website_id'] = $data['websiteId'];
        $mainTable['name']       = $data['name'];

        return $mainTable;
    }

    public function aggregateToUpdate(Aggregate $aggregate): array
    {
        $data = $this->hydrator->extract($aggregate);

        $mainTable = [];

        $mainTable['id']         = $data['id']->getId();
        $mainTable['website_id'] = $data['websiteId'];
        $mainTable['name']       = $data['name'];

        return $mainTable;
    }
}
