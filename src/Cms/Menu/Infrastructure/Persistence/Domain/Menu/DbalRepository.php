<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Persistence\Domain\Menu;

use Tulia\Cms\Menu\Domain\Menu\Exception\MenuNotFoundException;
use Tulia\Cms\Menu\Domain\Menu\Model\RepositoryInterface;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\AggregateId;
use Tulia\Cms\Menu\Domain\Menu\Model\Aggregate\Menu;
use Tulia\Cms\Menu\Domain\Menu\Model\ValueObject\ItemId;
use Tulia\Cms\Menu\Infrastructure\Persistence\Domain\Item\DbalRepository as ItemDbalRepository;
use Tulia\Framework\Database\ConnectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DbalRepository implements RepositoryInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var DataTransformer
     */
    protected $transformer;

    /**
     * @var ItemDbalRepository
     */
    protected $itemDbalRepository;

    /**
     * @param ConnectionInterface $connection
     * @param DataTransformer $transformer
     * @param ItemDbalRepository $itemDbalRepository
     */
    public function __construct(
        ConnectionInterface $connection,
        DataTransformer $transformer,
        ItemDbalRepository $itemDbalRepository
    ) {
        $this->connection  = $connection;
        $this->transformer = $transformer;
        $this->itemDbalRepository = $itemDbalRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find(AggregateId $id): Menu
    {
        $menu = $this->connection->fetchAll('
            SELECT *
            FROM #__menu AS tm
            WHERE tm.id = :id
            LIMIT 1', [
            'id' => $id->getId(),
        ]);

        if (empty($menu)) {
            throw new MenuNotFoundException();
        }

        $menu = reset($menu);
        $menu['items'] = $this->itemDbalRepository->findItems($id);

        return $this->transformer->arrayToAggregate($menu);
    }

    /**
     * @param Menu $menu
     */
    public function save(Menu $menu): void
    {
        $this->connection->transactional(function () use ($menu) {
            if ($this->recordExists($menu->getId()->getId())) {
                $this->update($menu);
            } else {
                $this->insert($menu);
            }

            foreach ($menu->getItemsChanges() as $change) {
                $id = $change['id'];

                if ($change['type'] === 'update') {
                    $this->itemDbalRepository->save($menu->getItem(new ItemId($id)));
                }
                if ($change['type'] === 'add') {
                    $this->itemDbalRepository->save($menu->getItem(new ItemId($id)));
                }
                if ($change['type'] === 'remove') {
                    $this->itemDbalRepository->delete(new ItemId($id));
                }
            }
        });
    }

    private function insert(Menu $menu): void
    {
        $this->connection->insert(
            '#__menu',
            $this->transformer->aggregateToInsert($menu)
        );
    }

    private function update(Menu $menu): void
    {
        $this->connection->update(
            '#__menu',
            $this->transformer->aggregateToUpdate($menu),
            ['id' => $menu->getId()->getId()]
        );
    }

    /**
     * @param Menu $menu
     */
    public function delete(Menu $menu): void
    {
        $this->connection->transactional(function () use ($menu) {
            $this->connection->delete('#__menu', ['id' => $menu->getId()->getId()]);
        });
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    private function recordExists(string $id): bool
    {
        $result = $this->connection->fetchAll('SELECT id FROM #__menu WHERE id = :id LIMIT 1', ['id' => $id]);

        return isset($result[0]['id']) && $result[0]['id'] === $id;
    }
}
