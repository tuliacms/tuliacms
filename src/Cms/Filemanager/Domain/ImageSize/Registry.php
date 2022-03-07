<?php

declare(strict_types=1);

namespace Tulia\Cms\Filemanager\Domain\ImageSize;

/**
 * @author Adam Banaszkiewicz
 */
class Registry implements ImageSizeRegistryInterface
{
    /** @var ImagesSizeProviderInterface[] */
    protected iterable $providers;
    /** @var ImageSize[] */
    protected array $sizes = [];

    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function all(): array
    {
        $this->resolve();

        return $this->sizes;
    }

    public function has(string $name): bool
    {
        $this->resolve();

        foreach ($this->sizes as $size) {
            if ($size->getCode() === $name) {
                return true;
            }
        }

        return isset($this->sizes[$name]);
    }

    public function get(string $name): ImageSize
    {
        $this->resolve();

        return $this->sizes[$name];
    }

    private function resolve(): void
    {
        if ($this->sizes !== []) {
            return;
        }

        foreach ($this->providers as $provider) {
            foreach ($provider->provide() as $size) {
                $name = $size['name'];

                $this->sizes[$name] = array_merge([
                    'name' => $name,
                    'label' => $name,
                    'translation_domain' => null,
                    'width' => null,
                    'height' => null,
                    'mode' => 'fit',
                ], $size);

                $this->sizes[$name]['width'] = $this->sizes[$name]['width'] === null
                    ? null
                    : (int) $this->sizes[$name]['width']
                ;
                $this->sizes[$name]['height'] = $this->sizes[$name]['height'] === null
                    ? null
                    : (int) $this->sizes[$name]['height']
                ;

                $this->sizes[$name] = ImageSize::fromArray($this->sizes[$name]);
            }
        }
    }
}
