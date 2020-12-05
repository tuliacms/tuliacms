<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\UI\Web\Controller\Frontend;

use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Homepage extends AbstractController
{
    /**
     * @return ViewInterface
     */
    public function index(): ViewInterface
    {
        return $this->view('@cms/homepage/index.tpl');
    }
}
