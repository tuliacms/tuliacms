<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Infrastructure\Persistence\Domain;

use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
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
     * @var CurrentWebsiteInterface
     */
    protected $currentWebsite;

    /**
     * @param ConnectionInterface $connection
     * @param CurrentWebsiteInterface $currentWebsite
     */
    public function __construct(
        ConnectionInterface $connection,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->connection     = $connection;
        $this->currentWebsite = $currentWebsite;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(array $term): void
    {
        if (empty($term['locale'])) {
            $term['locale'] = $this->currentWebsite->getLocale()->getCode();
        }

        $mainTable = [];
        $langTable = [];

        $mainTable['id'] = $term['id'];
        $mainTable['parent_id'] = empty($term['parentId']) ? null : $term['parentId'];
        $mainTable['level'] = $this->calculateLevel($term['parentId']);
        $mainTable['type'] = $term['type'];
        $mainTable['website_id'] = $term['websiteId'];

        $langTable['term_id'] = $term['id'];
        $langTable['locale'] = $term['locale'];
        $langTable['name'] = $term['name'];
        $langTable['slug'] = $term['slug'];
        $langTable['visibility'] = (int) $term['visibility'];

        $this->connection->insert('#__term', $mainTable);
        $this->connection->insert('#__term_lang', $langTable);

        foreach ($this->currentWebsite->getLocales() as $i => $locale) {
            // Skip current locale, is already in DB.
            if ($term['locale'] === $locale->getCode()) {
                continue;
            }

            $langTable['locale'] = $locale->getCode();
            $langTable['autogenerated_locale'] = 1;

            $this->connection->insert('#__term_lang', $langTable);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $term): void
    {
        if (empty($term['locale'])) {
            $term['locale'] = $this->currentWebsite->getLocale()->getCode();
        }

        $mainTable = [];
        $langTable = [];

        $mainTable['id'] = $term['id'];
        $mainTable['parent_id'] = empty($term['parentId']) ? null : $term['parentId'];
        $mainTable['level'] = $this->calculateLevel($term['parentId']);
        $mainTable['type'] = $term['type'];
        $mainTable['website_id'] = $term['websiteId'];

        $langTable['term_id'] = $term['id'];
        $langTable['locale'] = $term['locale'];
        $langTable['name'] = $term['name'];
        $langTable['slug'] = $term['slug'];
        $langTable['visibility'] = (int) $term['visibility'];
        $langTable['autogenerated_locale'] = 0;

        $this->connection->update('#__term', $mainTable, ['id' => $term['id']]);

        /**
         * Update in two steps. First update current locale.
         * Next update all rows with autogenerated_locale=1,
         * when update node in default locale.
         */
        $this->connection->update('#__term_lang', $langTable, ['term_id' => $term['id'], 'locale' => $term['locale']]);

        if ($this->currentWebsite->getLocale()->getCode() === $term['locale']) {
            unset($langTable['autogenerated_locale']);
            unset($langTable['locale']);
            $this->connection->update('#__term_lang', $langTable, ['term_id' => $term['id'], 'autogenerated_locale' => 1]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete(array $term): void
    {
        $this->connection->delete('#__term', ['id' => $term['id']]);
        $this->connection->delete('#__term_lang', ['term_id' => $term['id']]);
    }

    /**
     * @param string|null $parentId
     *
     * @return int
     */
    private function calculateLevel(?string $parentId): int
    {
        if (!$parentId) {
            return 0;
        }

        $level = $this->connection->fetchColumn('SELECT `level` FROM #__term WHERE id = :id LIMIT 1', [
            'id' => $parentId,
        ]);

        if ($level === null) {
            return 0;
        }

        return $level + 1;
    }
}
