<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Domain\WriteModel;

use Tulia\Cms\Attributes\Domain\WriteModel\AttributesRepository;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Exception\ContentTypeNotExistsException;
use Tulia\Cms\Node\Domain\WriteModel\Model\Node;
use Tulia\Cms\Node\Domain\WriteModel\Service\NodeWriteStorageInterface;
use Tulia\Cms\Shared\Domain\WriteModel\ActionsChain\AggregateActionsChainInterface;
use Tulia\Cms\Shared\Infrastructure\Utils\Uuid\UuidGeneratorInterface;
use Tulia\Component\Routing\Website\CurrentWebsiteInterface;

/**
 * @author Adam Banaszkiewicz
 */
class NodeRepository
{
    private NodeWriteStorageInterface $storage;
    private CurrentWebsiteInterface $currentWebsite;
    private AttributesRepository $attributeRepository;
    private UuidGeneratorInterface $uuidGenerator;
    private AggregateActionsChainInterface $actionsChain;
    private ContentTypeRegistryInterface $contentTypeRegistry;

    public function __construct(
        NodeWriteStorageInterface $storage,
        CurrentWebsiteInterface $currentWebsite,
        AttributesRepository $attributeRepository,
        UuidGeneratorInterface $uuidGenerator,
        AggregateActionsChainInterface $actionsChain,
        ContentTypeRegistryInterface $contentTypeRegistry
    ) {
        $this->storage = $storage;
        $this->currentWebsite = $currentWebsite;
        $this->attributeRepository = $attributeRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->actionsChain = $actionsChain;
        $this->contentTypeRegistry = $contentTypeRegistry;
    }

    public function createNew(string $nodeType): Node
    {
        $this->contentTypeRegistry->get($nodeType);

        return Node::createNew(
            $this->uuidGenerator->generate(),
            $nodeType,
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode()
        );
    }

    /**
     * @throws ContentTypeNotExistsException
     * @throws \Exception
     */
    public function find(string $id): ?Node
    {
        $node = $this->storage->find(
            $id,
            $this->currentWebsite->getId(),
            $this->currentWebsite->getLocale()->getCode(),
            $this->currentWebsite->getDefaultLocale()->getCode()
        );

        if (empty($node)) {
            return null;
        }

        $contentType = $this->contentTypeRegistry->get($node['type']);
        $node['attributes'] = $this->attributeRepository->findAll('node', $id, $contentType->buildAttributesMapping());

        $node = Node::fromArray($node);

        $this->actionsChain->execute('find', $node);

        return $node;
    }

    public function insert(Node $node): void
    {
        $this->storage->beginTransaction();

        try {
            $data = $node->toArray();

            $this->storage->insert($data, $this->currentWebsite->getDefaultLocale()->getCode());
            $this->attributeRepository->persist(
                'node',
                $node->getId()->getValue(),
                $data['attributes']
            );
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }
    }

    public function update(Node $node): void
    {
        $this->storage->beginTransaction();

        try {
            $data = $node->toArray();

            $this->storage->update($data, $this->currentWebsite->getDefaultLocale()->getCode());
            $this->attributeRepository->persist(
                'node',
                $node->getId()->getValue(),
                $data['attributes']
            );
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }
    }

    public function delete(Node $node): void
    {
        $this->storage->beginTransaction();

        try {
            $this->storage->delete($node->toArray());
            $this->attributeRepository->delete('node', $node->getId()->getValue());
            $this->storage->commit();
        } catch (\Exception $exception) {
            $this->storage->rollback();
            throw $exception;
        }
    }
}
