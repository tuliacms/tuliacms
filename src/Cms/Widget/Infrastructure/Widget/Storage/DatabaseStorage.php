<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Widget\Storage;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Widget\Storage\StorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseStorage implements StorageInterface
{
    private ConnectionInterface $connection;

    private CurrentWebsiteInterface $currentWebsite;

    private static array $cache = [];

    public function __construct(ConnectionInterface $connection, CurrentWebsiteInterface $currentWebsite)
    {
        $this->connection = $connection;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function all(?string $space): array
    {
        if (isset(static::$cache['all'])) {
            return static::$cache['all'];
        }

        return static::$cache['all'] = $this->connection->fetchAll('
            SELECT
                tm.*,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.payload_localized, tm.payload_localized) AS payload_localized
            FROM #__widget AS tm
            LEFT JOIN #__widget_lang AS tl
                ON tl.widget_id = tm.id AND tl.locale = :locale
            WHERE tm.website_id = :websiteId', [
            'websiteId' => $this->currentWebsite->getId(),
            'locale'    => $this->getLocale(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(string $id): ?array
    {
        if (isset(static::$cache[$id])) {
            return static::$cache[$id];
        }

        $result = $this->connection->fetchAll('
            SELECT
                tm.*,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.payload_localized, tm.payload_localized, "{}") AS payload_localized
            FROM #__widget AS tm
            LEFT JOIN #__widget_lang AS tl
                ON tl.widget_id = tm.id AND tl.locale = :locale
            WHERE tm.id = :id AND tm.website_id = :websiteId
            LIMIT 1', [
            'websiteId' => $this->currentWebsite->getId(),
            'locale'    => $this->getLocale(),
            'id'        => $id,
        ]);

        return static::$cache[$id] = $result[0] ?? null;
    }

    public function findBySpace(string $space): array
    {
        if (isset(static::$cache[$space])) {
            return static::$cache[$space];
        }

        return static::$cache[$space] = $this->connection->fetchAll('
            SELECT
                tm.*,
                COALESCE(tl.title, tm.title) AS title,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.payload_localized, tm.payload_localized, "{}") AS payload_localized
            FROM #__widget AS tm
            LEFT JOIN #__widget_lang AS tl
                ON tl.widget_id = tm.id AND tl.locale = :locale
            WHERE tm.space = :space AND tm.website_id = :websiteId
        ', [
            'websiteId' => $this->currentWebsite->getId(),
            'locale'    => $this->getLocale(),
            'space'     => $space,
        ]);
    }

    private function getLocale(): string
    {
        return $this->currentWebsite->getLocale()->getCode();
    }
}
