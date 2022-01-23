<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\ContentType;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Model\ContentType;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeStorageInterface;
use Tulia\Cms\Platform\Infrastructure\Bus\Event\EventBusInterface;
use Tulia\Cms\Shared\Ports\Infrastructure\Utils\Uuid\UuidGeneratorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeRepository
{
    private ContentTypeStorageInterface $storage;
    private UuidGeneratorInterface $uuidGenerator;
    private EventBusInterface $eventBus;
    private ContentTypeRegistry $contentTypeRegistry;

    public function __construct(
        ContentTypeStorageInterface $contentTypeStorage,
        UuidGeneratorInterface $uuidGenerator,
        EventBusInterface $eventBus,
        ContentTypeRegistry $contentTypeRegistry
    ) {
        $this->storage = $contentTypeStorage;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventBus = $eventBus;
        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    public function find(string $id): ?ContentType
    {
        foreach ($this->contentTypeRegistry->all() as $contentType) {
            if ($contentType->getId() === $id) {
                return $contentType;
            }
        }

        return null;
    }

    public function insert(ContentType $contentType): void
    {
        $this->storage->beginTransaction();

        try {
            $data = $this->extract($contentType);

            $this->storage->insert($data);
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        //$this->eventBus->dispatchCollection($node->collectDomainEvents());
    }

    public function update(ContentType $contentType): void
    {
        $this->storage->beginTransaction();

        try {
            $data = $this->extract($contentType);

            $this->storage->update($data);
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }

        //$this->eventBus->dispatchCollection($node->collectDomainEvents());
    }

    public function delete(ContentType $contentType): void
    {
        $this->storage->beginTransaction();

        try {
            $data = $this->extract($contentType);

            $this->storage->delete($data);
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }
    }

    public function extract(ContentType $contentType): array
    {
        $fields = [];

        foreach ($contentType->getFields() as $field) {
            $constraints = [];

            foreach ($field->getConstraints() as $code => $info) {
                $constraints[] = [
                    'code' => $code,
                    'modificators' => $info['modificators'],
                ];
            }

            $fields[] = [
                'code' => $field->getCode(),
                'type' => $field->getType(),
                'name' => $field->getName(),
                'is_multilingual' => $field->isMultilingual(),
                'is_multiple' => $field->isMultiple(),
                'is_internal' => $field->isInternal(),
                'configuration' => $field->getConfiguration(),
                'constraints' => $constraints,
            ];
        }

        $sections = [];

        foreach ($contentType->getLayout()->getSections() as $section) {
            $fieldsGroups = [];

            foreach ($section->getFieldsGroups() as $fieldsGroup) {
                $fieldsGroups[] = [
                    'code' => $fieldsGroup->getCode(),
                    'name' => $fieldsGroup->getName(),
                    'active' => $fieldsGroup->isActive(),
                    'interior' => $fieldsGroup->getInterior(),
                    'fields' => $fieldsGroup->getFields(),
                ];
            }

            $sections[] = [
                'code' => $section->getCode(),
                'field_groups' => $fieldsGroups,
            ];
        }

        $extracted = [
            'id' => $contentType->getId(),
            'type' => $contentType->getType(),
            'controller' => $contentType->getController(),
            'code' => $contentType->getCode(),
            'name' => $contentType->getName(),
            'icon' => $contentType->getIcon(),
            'is_routable' => $contentType->isRoutable(),
            'is_hierarchical' => $contentType->isHierarchical(),
            'is_internal' => $contentType->isInternal(),
            'routing_strategy' => $contentType->getRoutingStrategy(),
            'fields' => $fields,
            'layout' => [
                'code' => $contentType->getLayout()->getCode(),
                'name' => $contentType->getLayout()->getName(),
                'sections' => $sections,
            ],
        ];

        return $extracted;
    }
}
