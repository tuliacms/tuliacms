<?php

declare(strict_types=1);

namespace Tulia\Cms\Attributes\Domain\ReadModel\Service;

use Tulia\Cms\Attributes\Domain\ReadModel\ValueRender\ValueRendererInterface;

/**
 * @author Adam Banaszkiewicz
 */
class AttributesValueRenderer
{
    private ValueRendererInterface $valueRenderer;

    /**
     * @var array|string[]
     */
    private array $scopesByType;

    public function __construct(
        ValueRendererInterface $valueRenderer,
        array $scopes
    ) {
        $this->valueRenderer = $valueRenderer;
        $this->scopesByType = $scopes;
    }

    public function renderValues(array $values, string $type, string $scope): array
    {
        if ($values === []) {
            return $values;
        }

        if (\in_array($scope, $this->scopesByType[$type]['scopes'] ?? [], true) === false) {
            return $values;
        }

        foreach ($values as $key => $value) {
            if (strpos($key, ':compiled') !== false) {
                $values[$key] = $this->valueRenderer->render($value, [
                    'attribute' => $key
                ]);
            }
        }

        return $values;
    }
}
