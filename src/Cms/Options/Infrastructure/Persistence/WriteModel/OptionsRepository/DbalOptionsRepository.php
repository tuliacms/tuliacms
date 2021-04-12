<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Persistence\WriteModel\OptionsRepository;

use Tulia\Cms\Options\Domain\WriteModel\Model\Option;
use Tulia\Cms\Platform\Infrastructure\DataManipulation\Hydrator\HydratorInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalOptionsRepository implements OptionsRepositoryInterface
{
    private ConnectionInterface $connection;
    private HydratorInterface $hydrator;

    public function __construct(ConnectionInterface $connection, HydratorInterface $hydrator)
    {
        $this->connection = $connection;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function find(string $websiteId, string $name): ?Option
    {
        // TODO: Implement find() method.
    }

    /**
     * {@inheritdoc}
     */
    public function updateValue(string $name, $value, string $locale, string $websiteId, string $defaultLocale): void
    {
        if ($this->isMultilingualOption($name, $websiteId) === false || $locale === $defaultLocale) {
            $this->connection->update('#__option', [
                'value' => $value
            ], [
                'name' => $name,
                'website_id' => $websiteId,
            ], [
                'value' => \PDO::PARAM_STR,
                'name' => \PDO::PARAM_STR,
                'website_id' => \PDO::PARAM_STR,
            ]);

            return;
        }

        $query = 'INSERT INTO #__option_lang
            (`name`, `locale`, `value`) 
        VALUES
            (:name, :locale, :value) 
        ON DUPLICATE KEY UPDATE
            value = :value';

        $this->connection->executeUpdate($query, [
            'name' => $name,
            'locale' => $locale,
            'value' => $value,
        ], [
            'name' => \PDO::PARAM_STR,
            'locale' => \PDO::PARAM_STR,
            'value' => \PDO::PARAM_STR,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function create(Option $option): void
    {
        $this->bulkCreate([$option]);
    }

    /**
     * {@inheritdoc}
     */
    public function bulkCreate(array $options): void
    {
        $query = 'INSERT INTO #__option (`website_id`, `name`, `value`, `multilingual`, `autoload`) VALUES ';
        $values = [];
        $binds = [];
        $types = [];

        foreach ($options as $key => $option) {
            $data = $this->hydrator->extract($option);

            $values[] = str_replace(
                '{NUM}',
                $key,
                '(:website_id_{NUM}, :name_{NUM}, :value_{NUM}, :multilingual_{NUM}, :autoload_{NUM})',
            );

            $binds['website_id_' . $key] = $data['websiteId'];
            $binds['name_' . $key] = $data['name'];
            $binds['value_' . $key] = $data['value'];
            $binds['multilingual_' . $key] = $data['multilingual'];
            $binds['autoload_' . $key] = $data['autoload'];

            $types['website_id_' . $key] = \PDO::PARAM_STR;
            $types['name_' . $key] = \PDO::PARAM_STR;
            $types['value_' . $key] = \PDO::PARAM_STR;
            $types['multilingual_' . $key] = \PDO::PARAM_INT;
            $types['autoload_' . $key] = \PDO::PARAM_INT;
        }

        $query .= implode(', ', $values);

        $this->connection->executeUpdate($query, $binds, $types);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(string $name, string $websiteId): void
    {
        // TODO: Implement delete() method.
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
