<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\ContentProvider;

/**
 * @author Adam Banaszkiewicz
 */
trait SymfonyContainerStandarizableTrait
{
    protected function standarizeArray(array $data): array
    {
        foreach ($data['layout']['sections'] as $sectionCode => $section) {
            foreach ($section['groups'] as $groupCode => $group) {
                $data['layout']['sections'][$sectionCode]['groups'][$groupCode]['fields'] = $this->standarizeFields($group['fields']);
            }
        }

        return $data;
    }

    protected function standarizeFields(array $fields, ?string $parent = null): array
    {
        $result = [];

        foreach ($fields as $fieldCode => $field) {
            if ($field['parent'] !== $parent) {
                continue;
            }

            $constraints = [];
            $configuration = [];

            foreach ($field['constraints'] as $constraint) {
                $modificators = [];

                foreach ($constraint['modificators'] ?? [] as $modificator) {
                    $modificators[$modificator['code']] = $modificator['value'];
                }

                $constraints[$constraint['code']] = [
                    'modificators' => $modificators,
                ];
            }

            foreach ($field['configuration'] as $config) {
                $configuration[$config['code']] = $config['value'];
            }

            $fields[$fieldCode]['constraints'] = $constraints;
            $fields[$fieldCode]['configuration'] = $configuration;
            $fields[$fieldCode]['children'] = $this->standarizeFields($fields, $fieldCode);

            $result[$fieldCode] = $fields[$fieldCode];
        }

        return $result;
    }
}
