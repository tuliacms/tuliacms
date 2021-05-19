<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\Core;

use Tulia\Cms\Taxonomy\Domain\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\ActionsChain\TermActionInterface;
use Tulia\Cms\Taxonomy\Domain\WriteModel\Model\Term;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy\StrategyRegistry;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\TermPath\StorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class PathGenerator implements TermActionInterface
{
    private ConnectionInterface $connection;

    private StorageInterface $storage;

    private StrategyRegistry $strategyRegistry;

    private RegistryInterface $registry;

    private CurrentWebsiteInterface $currentWebsite;

    public function __construct(
        ConnectionInterface $connection,
        StorageInterface $storage,
        StrategyRegistry $strategyRegistry,
        RegistryInterface $registry,
        CurrentWebsiteInterface $currentWebsite
    ) {
        $this->connection = $connection;
        $this->storage = $storage;
        $this->strategyRegistry = $strategyRegistry;
        $this->registry = $registry;
        $this->currentWebsite = $currentWebsite;
    }

    public static function supports(): array
    {
        return [
            'insert' => 100,
            'update' => 100,
        ];
    }

    public function execute(Term $term): void
    {
        $strategy = $this->strategyRegistry->get(
            $this->registry->getType($term->getType())->getRoutingStrategy()
        );

        try {
            $this->connection->beginTransaction();

            foreach ($this->getRows($term->getId()->getId()) as $row) {
                if ($row['visibility'] !== '1') {
                    $this->storage->remove($term->getId()->getId(), $row['locale']);

                    continue;
                }

                $this->storage->save(
                    $term->getId()->getId(),
                    $row['locale'],
                    $strategy->generate($term->getId()->getId(), $row['locale'])
                );
            }

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();

            throw $e;
        }
    }

    private function getRows(string $id): array
    {
        return $this->connection->fetchAll('
            SELECT
                COALESCE(tl.slug, tm.slug) AS slug,
                COALESCE(tl.visibility, tm.visibility) AS visibility,
                COALESCE(tl.locale, :locale) AS locale
            FROM #__term AS tm
            LEFT JOIN #__term_lang AS tl
                ON tm.id = tl.term_id AND tl.locale = :locale
            WHERE tm.id = :id
            LIMIT 1', [
            'id' => $id,
            'locale' => $this->currentWebsite->getLocale()->getCode(),
        ]);
    }
}
