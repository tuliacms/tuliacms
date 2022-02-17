<?php

declare(strict_types=1);

namespace Tulia\Cms\FrontendToolbar\Builder;

use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\FrontendToolbar\Links\LinksCollection;
use Tulia\Cms\FrontendToolbar\Links\ProviderRegistry;
use Tulia\Component\Templating\EngineInterface;
use Tulia\Component\Templating\View;

/**
 * @author Adam Banaszkiewicz
 */
class Builder
{
    private ProviderRegistry $registry;

    private EngineInterface $engine;

    public function __construct(ProviderRegistry $registry, EngineInterface $engine)
    {
        $this->registry = $registry;
        $this->engine = $engine;
    }

    public function build(Request $request): string
    {
        $links = new LinksCollection();
        $contents = '';

        foreach ($this->registry->all() as $provider) {
            $provider->collect($links, $request);

            $contents .= $provider->provideContent($request);
        }

        return $this->engine->render(new View('@cms/frontend_toolbar/toolbar.tpl', [
            'links' => $links->all(),
            'contents' => $contents,
        ]));
    }
}
