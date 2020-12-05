<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\EventListener;

use Tulia\Cms\Taxonomy\Application\Event\TermEvent;
use Tulia\Cms\Taxonomy\Application\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Domain\Event\SlugChanged;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy\StrategyRegistry;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\TermPath\StorageInterface;
use Tulia\Cms\Taxonomy\Query\Enum\ScopeEnum;
use Tulia\Cms\Taxonomy\Query\FinderFactoryInterface;
use Tulia\Cms\Taxonomy\Query\Model\Term;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class PathGenerator
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var StrategyRegistry
     */
    protected $strategyRegistry;

    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param ConnectionInterface $connection
     * @param StorageInterface $storage
     * @param StrategyRegistry $strategyRegistry
     * @param RegistryInterface $registry
     */
    public function __construct(
        ConnectionInterface $connection,
        StorageInterface $storage,
        StrategyRegistry $strategyRegistry,
        RegistryInterface $registry
    ) {
        $this->connection = $connection;
        $this->storage = $storage;
        $this->strategyRegistry = $strategyRegistry;
        $this->registry = $registry;
    }

    /**
     * @param TermEvent $event
     */
    public function __invoke(TermEvent $event): void
    {
        $term = $event->getTerm();

        $strategy = $this->strategyRegistry->get(
            $this->registry->getType($term->getType())->getRoutingStrategy()
        );

        try {
            $this->connection->beginTransaction();

            foreach ($this->getRows($term->getId()) as $row) {
                if ($row['visibility'] !== '1') {
                    $this->storage->remove($term->getId(), $row['locale']);

                    continue;
                }

                $this->storage->save(
                    $term->getId(),
                    $row['locale'],
                    $strategy->generate($term->getId(), $row['locale'])
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
        return $this->connection->fetchAll('SELECT slug, locale, visibility FROM #__term_lang WHERE term_id = :id', [
            'id' => $id,
        ]);
    }
}
