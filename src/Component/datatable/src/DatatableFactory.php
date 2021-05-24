<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Datatable\Finder\FinderInterface;
use Tulia\Component\Datatable\Plugin\PluginsRegistry;
use Tulia\Component\Templating\EngineInterface;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableFactory
{
    private TranslatorInterface $translator;

    private PluginsRegistry $pluginsRegistry;

    private EngineInterface $engine;

    public function __construct(
        TranslatorInterface $translator,
        PluginsRegistry $pluginsRegistry,
        EngineInterface $engine
    ) {
        $this->translator = $translator;
        $this->pluginsRegistry = $pluginsRegistry;
        $this->engine = $engine;
    }

    public function create(FinderInterface $finder, Request $request): Datatable
    {
        return new Datatable(
            $finder,
            $request,
            $this->translator,
            $this->engine,
            $this->pluginsRegistry->getForFinder($finder)
        );
    }
}
