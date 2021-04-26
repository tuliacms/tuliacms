<?php

declare(strict_types=1);

namespace Tulia\Cms\Metadata\Syncer;

use Tulia\Cms\Metadata\MetadataInterface;

/**
 * @author Adam Banaszkiewicz
 */
interface SyncerInterface
{
    public function all(string $type, string $id): array;

    /**
     * @param MetadataInterface $metadata
     * @param string $type
     * @param string $id
     */
    public function pull(MetadataInterface $metadata, string $type, string $id): void;

    /**
     * @param MetadataInterface $metadata
     * @param string $type
     * @param string $id
     */
    public function push(MetadataInterface $metadata, string $type, string $id): void;

    /**
     * @param string $type
     * @param string $id
     * @param array $entries
     */
    public function delete(string $type, string $id, array $entries = []): void;
}
