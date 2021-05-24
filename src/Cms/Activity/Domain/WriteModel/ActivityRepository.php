<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Domain\WriteModel;

use Tulia\Cms\Activity\Domain\WriteModel\Model\ActivityRow;
use Tulia\Cms\Activity\Ports\Infrastructure\Persistence\Domain\WriteModel\ActivityWriteStorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ActivityRepository
{
    private ActivityWriteStorageInterface $storage;

    private CurrentWebsiteInterface $currentWebsite;

    private UuidGeneratorInterface $uuidGenerator;

    public function __construct(
        ActivityWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        UuidGeneratorInterface $uuidGenerator
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function createNew(): ActivityRow
    {
        return ActivityRow::createNew($this->uuidGenerator->generate(), $this->currentWebsite->getId());
    }

    public function save(ActivityRow $row): void
    {
        $this->storage->save($this->extract($row));
    }

    public function delete(ActivityRow $row): void
    {
        $this->storage->delete($row->getId());
    }

    private function extract(ActivityRow $row): array
    {
        return [
            'id' => $row->getId(),
            'website_id' => $row->getWebsiteId(),
            'message' => $row->getMessage(),
            'translation_domain' => $row->getTranslationDomain(),
            'context' => json_encode($row->getContext()),
            'created_at' => $row->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
