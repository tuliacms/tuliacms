<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Importer;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\ArrayToWriteModelTransformer;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentTypeRepository;
use Tulia\Component\Importer\ObjectImporter\ObjectImporterInterface;
use Tulia\Component\Importer\Structure\ObjectData;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeEntryImporter implements ObjectImporterInterface
{
    private ContentTypeRepository $repository;
    private ArrayToWriteModelTransformer $arrayToWriteModelTransformer;

    public function __construct(
        ContentTypeRepository $repository,
        ArrayToWriteModelTransformer $arrayToWriteModelTransformer
    ) {
        $this->repository = $repository;
        $this->arrayToWriteModelTransformer = $arrayToWriteModelTransformer;
    }

    public function import(ObjectData $objectData): ?array
    {
        $currentModel = $this->repository->findByCode($objectData['code']);

        if ($currentModel) {
            $id = $currentModel->getId();
        } else {
            $id = $this->repository->generateId();
        }

        $data = $objectData->toArray();
        $data['id'] = $id;

        $contentType = $this->arrayToWriteModelTransformer->transform($data);

        if ($currentModel) {
            $this->repository->update($contentType);
        } else {
            $this->repository->insert($contentType);
        }

        return null;
    }
}
