<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Application\UseCase;

use Tulia\Cms\Node\Domain\WriteModel\Event\NodeDeleted;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\NodeRepository;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
final class DeleteNode
{
    private NodeRepository $repository;
    private EventBusInterface $eventBus;
    private AggregateActionsChainInterface $actionsChain;

    public function __construct(
        NodeRepository $repository,
        EventBusInterface $eventBus,
        AggregateActionsChainInterface $actionsChain
    ) {
        $this->repository = $repository;
        $this->eventBus = $eventBus;
        $this->actionsChain = $actionsChain;
    }

    public function __invoke(Node $node): void
    {
        $this->actionsChain->execute('delete', $node);

        try {
            $this->repository->delete($node);

            $this->eventBus->dispatch(NodeDeleted::fromNode($node));
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
