<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Domain\WriteModel;

use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Cms\Widget\Domain\WriteModel\Event\WidgetDeleted;
use Tulia\Cms\Widget\Domain\WriteModel\Event\WidgetUpdated;
use Tulia\Cms\Widget\Domain\WriteModel\Exception\WidgetNotFoundException;
use Tulia\Cms\Widget\Domain\WriteModel\Model\Widget;
use Tulia\Cms\Widget\Ports\Infrastructure\Persistence\Domain\WriteModel\WidgetWriteStorageInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;
use Tulia\Component\Widget\Registry\WidgetRegistryInterface;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetRepository
{
    private WidgetWriteStorageInterface $storage;

    private CurrentWebsiteInterface $currentWebsite;

    private WidgetRegistryInterface $widgetRegistry;

    private UuidGeneratorInterface $uuidGenerator;

    private EventBusInterface $eventBus;

    public function __construct(
        WidgetWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        WidgetRegistryInterface $widgetRegistry,
        UuidGeneratorInterface $uuidGenerator,
        EventBusInterface $eventBus
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->widgetRegistry = $widgetRegistry;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventBus = $eventBus;
    }

    public function createNew(string $widgetType): Widget
    {
        return Widget::createNew(
            $this->uuidGenerator->generate(),
            $this->widgetRegistry->get($widgetType),
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode()
        );
    }

    /**
     * @throws WidgetNotFoundException
     */
    public function find(string $id): Widget
    {
        $data = $this->storage->find(
            $id,
            $this->currentWebsite->getLocale()->getCode(),
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        if ($data === []) {
            throw new WidgetNotFoundException(sprintf('Widget %s not found.', $id));
        }

        $data['styles'] = json_decode($data['styles'], true);
        $data['payload'] = json_decode($data['payload'], true);
        $data['payload_localized'] = json_decode($data['payload_localized'], true);
        $data['widget_type'] = $this->widgetRegistry->get($data['widget_type']);

        return Widget::buildFromArray($data);
    }

    public function insert(Widget $widget): void
    {
        $config = $widget->getWidgetConfiguration();

        $widget->setPayload($config->allNotMultilingual());
        $widget->setPayloadLocalized($config->allMultilingual());

        $this->storage->beginTransaction();

        try {
            $this->storage->insert($this->extract($widget), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection($widget->collectDomainEvents());
    }

    public function update(Widget $widget): void
    {
        $config = $widget->getWidgetConfiguration();

        $widget->setPayload($config->allNotMultilingual());
        $widget->setPayloadLocalized($config->allMultilingual());

        $this->storage->beginTransaction();

        try {
            $this->storage->update($this->extract($widget), $this->currentWebsite->getDefaultLocale()->getCode());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatchCollection(array_merge($widget->collectDomainEvents(), [WidgetUpdated::fromWidget($widget)]));
    }

    public function delete(Widget $widget): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->delete($this->extract($widget));
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        $this->eventBus->dispatch(WidgetDeleted::fromWidget($widget));
    }

    private function extract(Widget $widget): array
    {
        return [
            'id' => $widget->getId()->getId(),
            'website_id' => $widget->getWebsiteId(),
            'widget_type' => $widget->getWidgetInstance()->getId(),
            'space' => $widget->getSpace(),
            'name' => $widget->getName(),
            'html_class' => $widget->getHtmlClass(),
            'html_id' => $widget->getHtmlId(),
            'styles' => json_encode($widget->getStyles()),
            'locale' => $widget->getLocale(),
            'visibility' => $widget->getVisibility(),
            'title' => $widget->getTitle(),
            'payload' => json_encode($widget->getPayload()),
            'payload_localized' => json_encode($widget->getPayloadLocalized()),
        ];
    }
}
