<?php

declare(strict_types=1);

namespace Tulia\Component\Datatable;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Datatable\Finder\FinderInterface;
use Tulia\Component\Datatable\Plugin\PluginsRegistry;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
class DatatableFactory
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PluginsRegistry
     */
    private $pluginsRegistry;

    public function __construct(TranslatorInterface $translator, PluginsRegistry $pluginsRegistry)
    {
        $this->translator = $translator;
        $this->pluginsRegistry = $pluginsRegistry;
    }

    public function create(FinderInterface $finder, Request $request): Datatable
    {
        return new Datatable(
            $finder,
            $request,
            $this->translator,
            $this->pluginsRegistry->getForFinder($finder)
        );
    }
}
