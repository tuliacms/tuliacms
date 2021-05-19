<?php

declare(strict_types=1);

namespace Tulia\Cms\Taxonomy\Application\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\Taxonomy\Application\Event\TermCreatedEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermEvent;
use Tulia\Cms\Taxonomy\Application\Event\TermUpdatedEvent;
use Tulia\Cms\Taxonomy\Application\Domain\TaxonomyType\RegistryInterface;
use Tulia\Cms\Taxonomy\Infrastructure\Framework\Routing\Strategy\StrategyRegistry;
use Tulia\Cms\Taxonomy\Infrastructure\Persistence\TermPath\StorageInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Persistence\DBAL\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class PathGenerator implements EventSubscriberInterface
{
    protected ConnectionInterface $connection;

    protected StorageInterface $storage;

    protected StrategyRegistry $strategyRegistry;

    protected RegistryInterface $registry;

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

    public static function getSubscribedEvents(): array
    {
        return [
            TermCreatedEvent::class => ['__invoke', 500],
            TermUpdatedEvent::class => ['__invoke', 500],
        ];
    }

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
