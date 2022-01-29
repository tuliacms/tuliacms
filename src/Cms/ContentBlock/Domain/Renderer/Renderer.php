<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBlock\Domain\Renderer;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Renderer
{
    private ContentTypeRegistry $contentTypeRegistry;
    private EngineInterface $engine;
    private string $environment;

    public function __construct(
        ContentTypeRegistry $contentTypeRegistry,
        EngineInterface $engine,
        string $environment
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->engine = $engine;
        $this->environment = $environment;
    }

    public function render(array $model): string
    {
        $result = [];

        foreach ($model['blocks'] ?? [] as $block) {
            $result[] = $this->renderBlock($block);
        }

        return implode('', $result);
    }

    private function renderBlock(array $block): string
    {
        $fields = ['__block' => $block];

        foreach ($block['fields'] as $name => $values) {
            $fields[$name] = new FieldValue($values);
        }

        if ($this->environment === 'dev') {
            $fallbackView = '@cms/content_block/empty-block.debug.tpl';
        } else {
            $fallbackView = '@cms/content_block/empty-block.tpl';
        }

        return $this->engine->render(
            new View([
                /**
                 * @todo Configure modules, to use them as source of the views for blocks.
                 *       Some of the modules can import/export it's own the content types,
                 *       universally across whole system. So they also need to include
                 *       theirs own views for the blocks.
                 *       The best solution will be when we can configure those modules in
                 *       in YAML.
                 */
                '@theme/content-block/' . $block['type'] . '.tpl',
                // At the end, we have to add empty view, in case of any previous views are not defined.
                $fallbackView
            ], $fields)
        );
    }
}
