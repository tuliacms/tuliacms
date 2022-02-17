<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Domain\WriteModel;

use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Cms\Website\Domain\WriteModel\WebsiteStorageInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalStorage implements WebsiteStorageInterface
{
    protected ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $id): ?array
    {
        $website = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__website AS tm WHERE tm.id = :id LIMIT 1',
            ['id' => $id]
        );

        if ($website === []) {
            return null;
        }

        $website = reset($website);
        $website['locales'] = $this->connection->fetchAllAssociative(
            'SELECT * FROM #__website_locale AS tm WHERE tm.website_id = :id',
            ['id' => $id]
        );

        return $website;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $website): void
    {
        $this->connection->transactional(function () use ($website) {
            $websiteRow = [];
            $websiteRow['id']   = $website['id'];
            $websiteRow['name'] = $website['name'];
            $websiteRow['backend_prefix'] = $website['backend_prefix'];
            $websiteRow['active'] = $website['active'] ? '1' : '0';

            $this->connection->insert('#__website', $websiteRow);

            foreach ($website['locales'] as $locale) {
                $this->connection->insert('#__website_locale', [
                    'website_id'    => $website['id'],
                    'code'          => $locale['code'],
                    'domain'        => $locale['domain'],
                    'domain_development' => $locale['domain_development'],
                    'path_prefix'   => $locale['path_prefix'],
                    'ssl_mode'      => $locale['ssl_mode'],
                    'locale_prefix' => $locale['locale_prefix'],
                    'is_default'    => $locale['is_default'] ? '1' : '0',
                ]);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $website): void
    {
        $this->connection->transactional(function () use ($website) {
            $websiteRow = [];
            $websiteRow['id']   = $website['id'];
            $websiteRow['name'] = $website['name'];
            $websiteRow['backend_prefix'] = $website['backend_prefix'];
            $websiteRow['active'] = $website['active'] ? '1' : '0';

            $currentLocales = $this->getLocales($website['id']);

            $localesToAdd    = $this->collectLocalesToAdd($currentLocales, $website['locales']);
            $localesToRemove = $this->collectLocalesToRemove($currentLocales, $website['locales']);
            $localesToUpdate = $this->collectLocalesToUpdate($currentLocales, $website['locales'], array_merge($localesToAdd, $localesToRemove));

            $this->connection->update('#__website', $websiteRow, ['id' => $websiteRow['id']]);

            foreach ($localesToRemove as $code) {
                $this->connection->delete('#__website_locale', ['code' => $code]);
            }

            foreach ($localesToAdd as $code) {
                foreach ($website['locales'] as $locale) {
                    if ($locale['code'] === $code) {
                        $this->connection->insert('#__website_locale', [
                            'website_id'    => $website['id'],
                            'code'          => $locale['code'],
                            'domain'        => $locale['domain'],
                            'domain_development' => $locale['domain_development'],
                            'path_prefix'   => $locale['path_prefix'],
                            'ssl_mode'      => $locale['ssl_mode'],
                            'locale_prefix' => $locale['locale_prefix'],
                            'is_default'    => $locale['is_default'] ? '1' : '0',
                        ]);
                    }
                }
            }

            foreach ($localesToUpdate as $locale) {
                $this->connection->update('#__website_locale', [
                    'website_id'    => $website['id'],
                    'code'          => $locale['code'],
                    'domain'        => $locale['domain'],
                    'domain_development' => $locale['domain_development'],
                    'path_prefix'   => $locale['path_prefix'],
                    'ssl_mode'      => $locale['ssl_mode'],
                    'locale_prefix' => $locale['locale_prefix'],
                    'is_default'    => $locale['is_default'] ? '1' : '0',
                ], [
                    'website_id'    => $website['id'],
                    'code'   => $locale['code'],
                ]);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $id): void
    {
        $this->connection->transactional(function () use ($id) {
            $this->connection->delete('#__website', ['id' => $id]);
            $this->connection->delete('#__website_locale', ['website_id' => $id]);
        });
    }

    private function getLocales(string $id): array
    {
        $result = $this->connection->fetchAllAssociative('SELECT * FROM #__website_locale WHERE website_id = :id', ['id' => $id]);

        foreach ($result as $key => $val) {
            $result[$key]['is_default'] = $result[$key]['is_default'] === '1';
        }

        return $result;
    }

    private function collectLocalesToAdd(array $current, array $new): array
    {
        $newCodes     = $this->getCodes($new);
        $currentCodes = $this->getCodes($current);

        return array_values(array_diff($newCodes, array_intersect($newCodes, $currentCodes)));
    }

    private function collectLocalesToRemove(array $current, array $new): array
    {
        $newCodes     = $this->getCodes($new);
        $currentCodes = $this->getCodes($current);

        return array_values(array_diff($currentCodes, array_intersect($newCodes, $currentCodes)));
    }

    private function getCodes(array $locales): array
    {
        return array_map(static function ($item) { return $item['code']; }, $locales);
    }

    private function collectLocalesToUpdate(array $current, array $new, array $ommit): array
    {
        $result = [];

        foreach ($current as $currentItem) {
            if (\in_array($currentItem['code'], $ommit, true)) {
                continue;
            }

            foreach ($new as $newItem) {
                if ($newItem['code'] === $currentItem['code'] && $this->areSame($newItem, $currentItem) === false) {
                    $result[] = $newItem;
                }
            }
        }

        return $result;
    }

    private function areSame(array $newItem, array $currentItem): bool
    {
        return $newItem['domain'] === $currentItem['domain']
            && $newItem['domain_development'] === $currentItem['domain_development']
            && $newItem['locale_prefix'] === $currentItem['locale_prefix']
            && $newItem['path_prefix'] === $currentItem['path_prefix']
            && $newItem['ssl_mode'] === $currentItem['ssl_mode']
            && $newItem['is_default'] === $currentItem['is_default'];
    }
}
