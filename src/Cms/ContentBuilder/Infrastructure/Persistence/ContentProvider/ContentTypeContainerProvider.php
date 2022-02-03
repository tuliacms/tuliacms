<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\Infrastructure\Persistence\ContentProvider;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\AbstractContentTypeProvider;

/**
 * @author Adam Banaszkiewicz
 */
class ContentTypeContainerProvider extends AbstractContentTypeProvider
{
    private array $configuration;

    public function __construct(array $configuration)
    {
        $this->configuration = $configuration;
    }

    public function provide(): array
    {
        $result = [];

        foreach ($this->configuration as $code => $type) {
            $type['code'] = $code;
            $type = $this->standarizeArray($type);

            $result[] = $this->buildFromArray($type);
        }

        return $result;
    }

    private function standarizeArray(array $data): array
    {
        foreach ($data['layout']['sections'] as $sectionCode => $section) {
            foreach ($section['groups'] as $groupCode => $group) {
                foreach ($group['fields'] as $fieldCode => $field) {
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

                    $data['layout']['sections'][$sectionCode]['groups'][$groupCode]['fields'][$fieldCode]['constraints'] = $constraints;
                    $data['layout']['sections'][$sectionCode]['groups'][$groupCode]['fields'][$fieldCode]['configuration'] = $configuration;
                }
            }
        }

        return $data;
    }
}
