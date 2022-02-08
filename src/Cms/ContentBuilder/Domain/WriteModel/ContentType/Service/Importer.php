<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentTypeRepository;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\Model\ContentType;

/**
 * @author Adam Banaszkiewicz
 */
class Importer
{
    private ContentTypeRepository $repository;
    private ArrayToWriteModelTransformer $arrayToModelTransformer;

    public function __construct(
        ContentTypeRepository $repository,
        ArrayToWriteModelTransformer $arrayToModelTransformer
    ) {
        $this->repository = $repository;
        $this->arrayToModelTransformer = $arrayToModelTransformer;
    }

    public function importFromArray(array $type): ContentType
    {
        $currentModel = $this->repository->findByCode($type['code']);

        if ($currentModel) {
            $id = $currentModel->getId();
        } else {
            $id = $this->repository->generateId();
        }

        $type['id'] = $id;
        // @todo Validate structure of inported array
        $model = $this->arrayToModelTransformer->transform($type);

        if ($currentModel) {
            // @todo Update existing model instead of pass new model to Repository.
            $this->repository->update($model);
        } else {
            $this->repository->insert($model);
        }

        return $model;
    }
}