<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Framework\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Node\Domain\ReadModel\Persistence\CategoriesPersistenceInterface;
use Tulia\Cms\Node\Domain\ReadModel\Persistence\FlagsPersistenceInterface;
use Tulia\Cms\Node\Domain\WriteModel\Event\AttributeUpdated;

/**
 * @author Adam Banaszkiewicz
 */
class AttributeUpdatedListener implements EventSubscriberInterface
{
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private FlagsPersistenceInterface $flagsPersistence;
    private CategoriesPersistenceInterface $categoriesPersistence;

    public function __construct(
        ContentTypeRegistryInterface $contentTypeRegistry,
        FlagsPersistenceInterface $flagsPersistence,
        CategoriesPersistenceInterface $categoriesPersistence
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->flagsPersistence = $flagsPersistence;
        $this->categoriesPersistence = $categoriesPersistence;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AttributeUpdated::class => ['processUpdate', 0],
        ];
    }

    public function processUpdate(AttributeUpdated $event): void
    {
        if ($event->getAttribute() === 'flags') {
            $this->flagsPersistence->update($event->getNodeId(), $event->getValue() ?? []);
            return;
        }

        $type = $this->contentTypeRegistry->get($event->getNodeType());

        if ($type->hasField($event->getAttribute())) {
            $field = $type->getField($event->getAttribute());

            if ($field->getType() === 'taxonomy') {
                $this->categoriesPersistence->update(
                    $event->getNodeId(),
                    $field->getTaxonomy(),
                    $event->getValue() ? [$event->getValue() => 'MAIN'] : []
                );
            }
        }
    }
}
