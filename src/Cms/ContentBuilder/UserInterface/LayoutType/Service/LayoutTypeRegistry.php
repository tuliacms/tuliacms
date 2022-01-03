<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Service;

use Tulia\Cms\ContentBuilder\UserInterface\LayoutType\Model\LayoutType;

/**
 * @author Adam Banaszkiewicz
 */
class LayoutTypeRegistry
{
    /**
     * @var LayoutType[]
     */
    protected array $layoutTypes = [];

    /**
     * @var LayoutTypeProviderInterface[]
     */
    protected array $layoutTypeProviders = [];

    public function addProvider(LayoutTypeProviderInterface $layoutTypeProvider): void
    {
        $this->layoutTypeProviders[] = $layoutTypeProvider;
    }

    public function get(string $type): LayoutType
    {
        $this->fetch();

        return $this->layoutTypes[$type];
    }

    public function all(): array
    {
        $this->fetch();

        return $this->layoutTypes;
    }

    public function has(string $type): bool
    {
        $this->fetch();

        return isset($this->layoutTypes[$type]);
    }

    public function getTypes(): array
    {
        $this->fetch();

        return array_keys($this->layoutTypes);
    }

    private function fetch(): void
    {
        if ($this->layoutTypes !== []) {
            return;
        }

        $types = [];

        foreach ($this->layoutTypeProviders as $provider) {
            $types[] = $provider->provide();
        }

        foreach (array_merge(...$types) as $type) {
            $this->layoutTypes[$type->getCode()] = $type;
        }
    }
}
