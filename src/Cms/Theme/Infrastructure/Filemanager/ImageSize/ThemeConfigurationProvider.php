<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Filemanager\ImageSize;

use Tulia\Cms\Filemanager\Application\ImageSize\ProviderInterface;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeConfigurationProvider implements ProviderInterface
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
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
