<?php

declare(strict_types=1);

namespace Tulia\Cms\ImportExport\UserInterface\Web\Tiles;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollection;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesCollector;

/**
 * @author Adam Banaszkiewicz
 */
class ToolsTilesCollector implements DashboardTilesCollector
{
    protected RouterInterface $router;
    protected TranslatorInterface $translator;

    public function __construct(RouterInterface $router, TranslatorInterface $translator)
    {
        $this->router = $router;
        $this->translator = $translator;
    }

    public function collect(DashboardTilesCollection $collection): void
    {
        $collection
            ->add('importer', [
                'name' => $this->translator->trans('importer', [], 'import_export'),
                'link' => $this->router->generate('backend.import_export.importer'),
                'icon' => 'fas fa-file-import',
            ])
        ;
    }

    public function supports(string $group): bool
    {
        return $group === 'tools';
    }
}
