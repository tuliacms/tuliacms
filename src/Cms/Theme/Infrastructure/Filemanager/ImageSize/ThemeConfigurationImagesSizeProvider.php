<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Filemanager\ImageSize;

use Tulia\Cms\Filemanager\Domain\ImageSize\ImagesSizeProviderInterface;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeConfigurationImagesSizeProvider implements ImagesSizeProviderInterface
{
    protected ManagerInterface $manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function provide(): array
    {
        $sizes = [];

        foreach ($this->manager->getTheme()->getConfig()->all('image_size') as $name => $size) {
            $sizes[$name] = [
                'width'  => $size['width'] ?? 0,
                'height' => $size['height'] ?? 0,
                'label'  => $size['label'] ?? $name,
                'translation_domain' => $size['translation_domain'] ?? null,
            ];
        }

        return $sizes;
    }
}
