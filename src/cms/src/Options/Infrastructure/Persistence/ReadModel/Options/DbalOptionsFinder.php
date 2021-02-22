<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Persistence\ReadModel\Options;

use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalOptionsFinder implements OptionsFinderInterface
{
    /**
     * @var ConnectionInterface
     */
    private $connection;

    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function findByName(string $name, string $locale, string $website)
    {
        $result = $this->connection->fetchColumn('SELECT COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_lang tl
                ON tm.name = tl.name AND tl.locale = :locale
            WHERE tm.`name` = :name AND tm.website_id = :websiteId
            LIMIT 1', [
            'name'      => $name,
            'locale'    => $locale,
            'websiteId' => $website,
        ], 0, [
            'name'      => \PDO::PARAM_STR,
            'locale'    => \PDO::PARAM_STR,
            'websiteId' => \PDO::PARAM_STR,
        ]);

        return \is_bool($result) ? null : $result;
    }

    public function findBulkByName(array $names, string $locale, string $website): array
    {
        $result = $this->connection->fetchColumn('SELECT tm.name, COALESCE(tl.`value`, tm.`value`) AS `value`
            FROM #__option tm
            LEFT JOIN #__option_lang tl
                ON tm.name = tl.name AND tl.locale = :locale
            WHERE tm.`name` IN (:name) AND tm.website_id = :websiteId
            LIMIT 1', [
            'name'      => $names,
            'locale'    => $locale,
            'websiteId' => $website,
        ], 0, [
            'name'      => ConnectionInterface::PARAM_ARRAY_STR,
            'locale'    => \PDO::PARAM_STR,
            'websiteId' => \PDO::PARAM_STR,
        ]);

        return array_column($result, 'value', 'name');
    }
}
