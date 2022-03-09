<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Importer;

use Tulia\Cms\Attributes\Infrastructure\Importer\ObjectDataToAttributesTransformer;
use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Node\Application\UseCase\CreateNode;
use Tulia\Cms\Node\Domain\WriteModel\NodeRepository;
use Tulia\Cms\User\Application\Service\AuthenticatedUserProviderInterface;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
class NodeEntryImporter implements ObjectImporterInterface
{
    private NodeRepository $repository;
    private CreateNode $createNode;
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private AuthenticatedUserProviderInterface $authenticatedUserProvider;

    public function __construct(
        NodeRepository $repository,
        CreateNode $createNode,
        ContentTypeRegistryInterface $contentTypeRegistry,
        AuthenticatedUserProviderInterface $authenticatedUserProvider
    ) {
        $this->repository = $repository;
        $this->createNode = $createNode;
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->authenticatedUserProvider = $authenticatedUserProvider;
    }

    public function import(ObjectData $objectData): ?array
    {
        ($this->createNode)($objectData['type'], $this->transformObjectDataToAttributes($objectData));

        return null;
    }

    private function transformObjectDataToAttributes(ObjectData $objectData): array
    {
        $transformer = new ObjectDataToAttributesTransformer(
            $this->contentTypeRegistry->get($objectData['type'])
        );
        $transformer->useObjectData($objectData->toArray()['attributes']);
        $transformer->useAdditionalData([
            'title' => $objectData['title'],
            'author_id' => $this->authenticatedUserProvider->getUser()->getId(),
        ]);

        return $transformer->transform();
    }
}
