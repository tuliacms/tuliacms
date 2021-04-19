<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Framework\Website\Storage;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\Locale\Locale;
use Tulia\Component\Routing\Website\Locale\Storage\StorageInterface;
use Tulia\Component\Routing\Website\Website;

/**
 * @author Adam Banaszkiewicz
 */
class DatabaseStorage
{
    protected StorageInterface $storage;
    protected ConnectionInterface $connection;
    private static array $cache = [];

    public function __construct(ConnectionInterface $connection, StorageInterface $storage)
    {
        $this->connection = $connection;
        $this->storage    = $storage;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        if (isset(static::$cache['all'])) {
            return static::$cache['all'];
        }

        $websites = [];

        foreach ($this->collectWebsitesWithLocales() as $source) {
            $locales = [];
            $defaultLocale = null;

            foreach ($source['locales'] as $row) {
                $locale = new Locale(
                    $row['code'],
                    $row['domain'],
                    $row['locale_prefix'],
                    $row['path_prefix'],
                    $row['ssl_mode'],
                );

                if ($row['is_default'] === 1) {
                    $locale->setDefault(true);
                    $defaultLocale = $locale;
                }

                $locales[] = $locale;
            }

            $websites[] = new Website(
                $source['id'],
                $locales,
                $defaultLocale,
                $source['backend_prefix'],
                $source['name']
            );
        }

        return static::$cache['all'] = $websites;
    }

    private function collectWebsitesWithLocales(): array
    {
        $sourceWebsites = $this->connection->fetchAll('SELECT * FROM #__website ORDER BY `name` ASC');
        $sourceLocales  = $this->connection->fetchAll('SELECT * FROM #__website_locale');
        $websites = [];

        foreach ($sourceWebsites as $website) {
            $locales = [];

            foreach ($sourceLocales as $locale) {
                if ($locale['website_id'] === $website['id']) {
                    $locales[] = [
                        'domain'        => $locale['domain'],
                        'path_prefix'   => $locale['path_prefix'],
                        'ssl_mode'      => $locale['ssl_mode'],
                        'code'   => $locale['code'],
                        'locale_prefix' => $locale['locale_prefix'],
                        'is_default'    => (int) $locale['is_default'],
                    ];
                }
            }

            /**
             * Do not add empty locales domain to prevent any
             * vulnerable operations in system.
             */
            if (empty($locales)) {
                continue;
            }

            $websites[] = [
                'id'      => $website['id'],
                'name'    => $website['name'],
                'backend_prefix' => $website['backend_prefix'],
                'locales' => $locales,
            ];
        }

        return $websites;
    }
}
