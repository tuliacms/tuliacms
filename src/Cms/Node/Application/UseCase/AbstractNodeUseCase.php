<?php

declare(strict_types=1);

namespace Tulia\Cms\User\Application\UseCase;

use Tulia\Cms\Attributes\Domain\WriteModel\Model\Attribute;
use Tulia\Cms\Node\Domain\WriteModel\Event\NodeUpdated;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\NodeRepository;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Domain\WriteModel\Model\ValueObject\ImmutableDateTime;
use Tulia\Cms\Shared\Infrastructure\Bus\Event\EventBusInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractNodeUseCase
{
    protected NodeRepository $repository;
    protected EventBusInterface $eventBus;
    protected AggregateActionsChainInterface $actionsChain;

    public function __construct(
        NodeRepository $repository,
        EventBusInterface $eventBus,
        AggregateActionsChainInterface $actionsChain
    ) {
        $this->repository = $repository;
        $this->eventBus = $eventBus;
        $this->actionsChain = $actionsChain;
    }

    protected function create(Node $node): void
    {
        $this->actionsChain->execute('insert', $node);

        try {
            $this->repository->insert($node);
            $this->eventBus->dispatchCollection($node->collectDomainEvents());
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    protected function update(Node $node): void
    {
        $this->actionsChain->execute('update', $node);

        try {
            $this->repository->update($node);
            $this->eventBus->dispatchCollection($node->collectDomainEvents());
            $this->eventBus->dispatch(NodeUpdated::fromNode($node));
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    /**
     * @param Attribute[] $attributes
     */
    protected function updateModel(Node $node, array $attributes): void
    {
        $getValue = function (string $code, $default = null) use ($attributes) {
            foreach ($attributes as $attribute) {
                if ($attribute->getCode() === $code) {
                    return $attribute->getValue();
                }
            }

            return $default;
        };

        $node->setStatus($getValue('status', 'published'));
        $node->setSlug($getValue('slug'));
        $node->setTitle($getValue('title'));
        $node->setPublishedAt(new ImmutableDateTime($getValue('published_at', '')));
        $node->setPublishedTo($getValue('published_to') ? new ImmutableDateTime($getValue('published_to', '')) : null);
        $node->setParentId($getValue('parent_id'));
        $node->setAuthorId($getValue('author_id'));
        $node->updateAttributes($attributes);
    }
}
