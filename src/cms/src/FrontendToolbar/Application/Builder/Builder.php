<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Application\Builder;

use Tulia\Cms\FrontendToolbar\Application\Links\Links;
use Tulia\Cms\FrontendToolbar\Application\Links\ProviderRegistry;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
class Builder
{
    /**
     * @var ProviderRegistry
     */
    private $registry;

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @param ProviderRegistry $registry
     */
    public function __construct(ProviderRegistry $registry, EngineInterface $engine)
    {
        $this->registry = $registry;
        $this->engine = $engine;
    }

    public function build(Request $request): string
    {
        $links = new Links();
        $contents = '';

        foreach ($this->registry->all() as $provider) {
            $provider->provideLinks($links, $request);

            $contents .= $provider->provideContent($request);
        }

        return $this->engine->render(new View('@cms/frontend_toolbar/toolbar.tpl', [
            'links' => $links->all(),
            'contents' => $contents,
        ]));
    }
}
