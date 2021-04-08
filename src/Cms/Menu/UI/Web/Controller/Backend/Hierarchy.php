<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\UI\Web\Controller\Backend;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;

/**
 * @author Adam Banaszkiewicz
 */
class Hierarchy extends AbstractController
{
    public function index(string $menuId)
    {
        return $this->view('@backend/menu/hierarchy/index.tpl');
    }
}
