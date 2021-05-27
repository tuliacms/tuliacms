<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Widget\Ports\Domain\Renderer\RendererInterface;
use Tulia\Component\Widget\Storage\StorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class WidgetExtension extends AbstractExtension
{
    private RendererInterface $renderer;

    private StorageInterface $storage;

    public function __construct(RendererInterface $renderer, StorageInterface $storage)
    {
        $this->renderer = $renderer;
        $this->storage = $storage;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('widget', function (string $id) {
                return $this->renderer->forId($id);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('widgets_space', function (string $space) {
                return $this->renderer->forSpace($space);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('widgets_space_count', function (string $space) {
                return count($this->storage->findBySpace($space));
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
