<?php

declare(strict_types=1);

namespace Tulia\Cms\Website\Infrastructure\Persistence\Domain;

use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalPersister
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $website): void
    {
        $websiteRow = [];
        $websiteRow['id']   = $website['id'];
        $websiteRow['name'] = $website['name'];
        $websiteRow['backend_prefix'] = $website['backend_prefix'];

        $this->connection->insert('#__website', $websiteRow);

        foreach ($website['locales'] as $locale) {
            $this->connection->insert('#__website_locale', [
                'website_id'    => $website['id'],
                'code'          => $locale['code'],
                'domain'        => $locale['domain'],
                'path_prefix'   => $locale['path_prefix'],
                'ssl_mode'      => $locale['ssl_mode'],
                'locale_prefix' => $locale['locale_prefix'],
                'is_default'    => $locale['is_default'] ? '1' : '0',
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $website): void
    {
        $websiteRow = [];
        $websiteRow['id']   = $website['id'];
        $websiteRow['name'] = $website['name'];
        $websiteRow['backend_prefix'] = $website['backend_prefix'];

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
                'path_prefix'   => $locale['path_prefix'],
                'ssl_mode'      => $locale['ssl_mode'],
                'locale_prefix' => $locale['locale_prefix'],
                'is_default'    => $locale['is_default'] ? '1' : '0',
            ], [
                'website_id'    => $website['id'],
                'code'   => $locale['code'],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(array $website): void
    {
        $this->connection->delete('#__website', ['id' => $website['id']]);
        $this->connection->delete('#__website_locale', ['website_id' => $website['id']]);
    }

    private function getLocales(string $id): array
    {
        $result = $this->connection->fetchAll('SELECT * FROM #__website_locale WHERE website_id = :id', ['id' => $id]);

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
            && $newItem['locale_prefix'] === $currentItem['locale_prefix']
            && $newItem['path_prefix'] === $currentItem['path_prefix']
            && $newItem['ssl_mode'] === $currentItem['ssl_mode']
            && $newItem['is_default'] === $currentItem['is_default'];
    }
}
