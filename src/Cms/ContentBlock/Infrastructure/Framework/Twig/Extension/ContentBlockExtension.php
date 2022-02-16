<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBlock\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\ContentBlock\Domain\Renderer\Renderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class ContentBlockExtension extends AbstractExtension
{
    private Renderer $renderer;

    public function __construct(Renderer $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_block_render', [$this, 'contentBlockRender'], ['is_safe' => [ 'html' ]]),
        ];
    }

    public function contentBlockRender(?string $source): string
    {
        $model = base64_decode($source);
        $model = json_decode($model, true, JSON_THROW_ON_ERROR);

        return $this->renderer->render($model);
    }
}
