<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Backend\Controller;

use Tulia\Cms\Homepage\UserInterface\Web\Backend\Tiles\DashboardTilesRegistryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Misc extends AbstractController
{
    private DashboardTilesRegistryInterface $tilesRegistry;

    public function __construct(DashboardTilesRegistryInterface $tilesRegistry)
    {
        $this->tilesRegistry = $tilesRegistry;
    }

    public function system(): ViewInterface
    {
        return $this->view('@backend/homepage/misc/system.tpl', [
            'tiles' => $this->tilesRegistry->getTiles('system'),
        ]);
    }

    public function tools(): ViewInterface
    {
        return $this->view('@backend/homepage/misc/tools.tpl', [
            'tiles' => $this->tilesRegistry->getTiles('tools'),
        ]);
    }
}
