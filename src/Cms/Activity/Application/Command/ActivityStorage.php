<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Application\Command;

use Tulia\Cms\Activity\Application\Model\Row;
use Tulia\Cms\Activity\Infrastructure\Persistence\Command\RepositoryInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ActivityStorage
{
    private RepositoryInterface $repository;
    private CurrentWebsiteInterface $currentWebsite;
    private UuidGeneratorInterface $uuidGenerator;
    private HydratorInterface $hydrator;

    public function __construct(
        RepositoryInterface $repository,
        CurrentWebsiteInterface $currentWebsite,
        UuidGeneratorInterface $uuidGenerator,
        HydratorInterface $hydrator
    ) {
        $this->repository = $repository;
        $this->currentWebsite = $currentWebsite;
        $this->uuidGenerator = $uuidGenerator;
        $this->hydrator = $hydrator;
    }

    public function save(Row $row): void
    {
        if ($row->hasId() === false) {
            $row->setId($this->uuidGenerator->generate());
        }

        if ($row->hasWebsiteId() === false) {
            $row->setWebsiteId($this->currentWebsite->getId());
        }

        $data = $this->hydrator->extract($row);
        $data['createdAt'] = $data['createdAt']->format('Y-m-d H:i:s');

        $this->repository->save($data);
    }

    public function delete(Row $row): void
    {
        $this->repository->delete($row->getId());
    }
}
