<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service;

use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentTypeRepository;

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

    public function importFromFile(string $filepath, string $format): void
    {
        if ($format === 'json') {
            $this->importFromFileJSON($filepath);
            return;
        }

        throw new \InvalidArgumentException('Importer supports only JSON files.');
    }

    private function importFromFileJSON(string $filepath): void
    {
        $content = file_get_contents($filepath);
        $model = json_decode($content, true, JSON_THROW_ON_ERROR);

        foreach ($model['types'] ?? [] as $type) {
            $this->importFromArray($type);
        }
    }

    public function importFromArray(array $type): void
    {
        $currentModel = $this->repository->findByCode($type['code']);

        if ($currentModel) {
            $id = $currentModel->getId();
        } else {
            $id = $this->repository->generateId();
        }

        foreach ($type['field_groups'] as $groupKey => $group) {
            $type['field_groups'][$groupKey]['fields'] = $this->flattenFields($group['fields']);
        }

        $type['id'] = $id;
        // @todo Validate structure of inported array
        $model = $this->arrayToModelTransformer->transform($type);

        if ($currentModel) {
            $this->repository->update($model);
        } else {
            $this->repository->insert($model);
        }
    }

    private function flattenFields(array $fields, ?string $parent = null): array
    {
        $result = [];

        foreach ($fields as $field) {
            $field['parent'] = $parent;
            $result[] = [$field];

            if (isset($field['children']) && $field['children'] !== []) {
                $result[] = $this->flattenFields($field['children'], $field['code']);
            }
        }

        return array_merge(...$result);
    }
}
