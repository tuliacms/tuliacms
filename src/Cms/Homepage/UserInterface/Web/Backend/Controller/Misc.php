<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UserInterface\Web\Backend\Controller;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Dashboard\Tiles\ManagerInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Misc extends AbstractController
{
    public function system(ManagerInterface $manager): ViewInterface
    {
        return $this->view('@backend/homepage/misc/system.tpl', [
            'tiles' => $manager->getTiles('system'),
        ]);
    }

    public function tools(ManagerInterface $manager): ViewInterface
    {
        return $this->view('@backend/homepage/misc/tools.tpl', [
            'tiles' => $manager->getTiles('tools'),
        ]);
    }
}
