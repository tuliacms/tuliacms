<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBlock\Domain\Renderer;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Attributes\Domain\ReadModel\Model\AttributeValue;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Renderer
{
    private ContentTypeRegistryInterface $contentTypeRegistry;
    private EngineInterface $engine;
    private string $environment;
    private array $paths;
    private string $fallbackView;

    public function __construct(
        ContentTypeRegistryInterface $contentTypeRegistry,
        EngineInterface $engine,
        string $environment,
        array $paths
    ) {
        $this->contentTypeRegistry = $contentTypeRegistry;
        $this->engine = $engine;
        $this->environment = $environment;
        $this->paths = $paths;

        $this->prepareViews();
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
            $fields[$name] = new AttributeValue($values);
        }

        $views = array_map(static function (string $path) use ($block) {
            return $path . $block['type'] . '.tpl';
        }, $this->paths);
        $views[] = $this->fallbackView;

        return $this->engine->render(
            new View($views, $fields)
        );
    }

    private function prepareViews(): void
    {
        if ($this->environment === 'dev') {
            $this->fallbackView = '@cms/content_block/empty-block.debug.tpl';
        } else {
            $this->fallbackView = '@cms/content_block/empty-block.tpl';
        }

        /**
         * Views priority:
         * - Theme views - This allows to overwrite views from modules
         * - Modules views
         * - Fallback views -  At the end, we have to add empty view, in case of any previous views are not defined.
         */
        array_unshift($this->paths, '@theme/content-block/');
    }
}
