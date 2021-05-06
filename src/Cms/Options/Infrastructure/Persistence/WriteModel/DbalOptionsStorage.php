<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Persistence\WriteModel;

use Tulia\Cms\Options\Ports\Infrastructure\Persistence\Domain\WriteModel\OptionsStorageInterface;
use Tulia\Cms\Platform\Infrastructure\Persistence\Domain\AbstractLocalizableStorage;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalOptionsStorage extends AbstractLocalizableStorage implements OptionsStorageInterface
{
    private ConnectionInterface $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function find(string $name, string $websiteId, string $locale): ?array
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT
                tm.*,
                COALESCE(tl.locale, :locale) AS `locale`,
                COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_lang tl
                ON tm.id = tl.option_id AND tl.locale = :locale
            WHERE tm.name = :name AND tm.website_id = :websiteId
            LIMIT 1', [
            'name' => $name,
            'locale' => $locale,
            'websiteId' => $websiteId,
        ], [
            'name' => \PDO::PARAM_STR,
            'locale' => \PDO::PARAM_STR,
            'websiteId' => \PDO::PARAM_STR,
        ]);

        return $result[0] ?? null;
    }

    public function findAllForWebsite(string $websiteId, string $locale): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT
                tm.*,
                COALESCE(tl.locale, :locale) AS `locale`,
                COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_lang tl
                ON tm.id = tl.option_id AND tl.locale = :locale
            WHERE tm.website_id = :websiteId', [
            'locale'    => $locale,
            'websiteId' => $websiteId,
        ], [
            'locale'    => \PDO::PARAM_STR,
            'websiteId' => \PDO::PARAM_STR,
        ]);
    }

    public function delete(array $option): void
    {
        $this->connection->delete('#__option', $option);
    }

    protected function updateMainRow(array $data, bool $foreignLocale): void
    {
        $mainTable = [];
        $mainTable['multilingual'] = $data['multilingual'] ? '1' : '0';
        $mainTable['autoload'] = $data['autoload'] ? '1' : '0';

        if ($foreignLocale === false || $this->isMultilingualOption($data['name'], $data['website_id']) === false) {
            $mainTable['value'] = $data['value'];
        }

        $this->connection->update('#__option', $mainTable, ['name' => $data['name'], 'website_id' => $data['website_id']]);
    }

    protected function insertMainRow(array $data): void
    {
        $mainTable = [];
        $mainTable['id'] = $data['id'];
        $mainTable['name'] = $data['name'];
        $mainTable['website_id'] = $data['website_id'];
        $mainTable['value'] = $data['value'];
        $mainTable['multilingual'] = $data['multilingual'] ? '1' : '0';
        $mainTable['autoload'] = $data['autoload'] ? '1' : '0';

        $this->connection->insert('#__option', $mainTable);
    }

    protected function insertLangRow(array $data): void
    {
        if ($this->isMultilingualOption($data['name'], $data['website_id']) === false) {
            return;
        }

        $langTable = [];
        $langTable['option_id'] = $data['id'];
        $langTable['locale'] = $data['locale'];
        $langTable['value'] = $data['value'];

        $this->connection->insert('#__option_lang', $langTable);
    }

    protected function updateLangRow(array $data): void
    {
        if ($this->isMultilingualOption($data['name'], $data['website_id']) === false) {
            return;
        }

        $langTable = [];
        $langTable['value'] = $data['value'];

        $this->connection->update('#__option_lang', $langTable, [
            'option_id' => $data['id'],
            'locale' => $data['locale'],
        ]);
    }

    protected function langExists(string $id, string $locale): bool
    {
        $result = $this->connection->fetchAllAssociative(
            'SELECT option_id FROM #__option_lang WHERE option_id = :id AND locale = :locale LIMIT 1',
            ['id' => $id, 'locale' => $locale]
        );

        return isset($result[0]['option_id']) && $result[0]['option_id'] === $id;
    }

    private function isMultilingualOption(string $name, string $website): bool
    {
        return (bool) $this->connection->createQueryBuilder()
            ->select('o.multilingual')
            ->from('#__option', 'o')
            ->andWhere('o.name = :name')
            ->andWhere('o.website_id = :website')
            ->setParameter('website', $website, \PDO::PARAM_STR)
            ->setParameter('name', $name, \PDO::PARAM_STR)
            ->execute()
            ->fetchColumn();
    }
}
