<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Query\Factory;

use Tulia\Cms\Widget\Query\Model\Widget;

/**
 * @author Adam Banaszkiewicz
 */
interface WidgetFactoryInterface
{
    /**
     * @param array $data
     *
     * @return Widget
     */
    public function createNew(array $data = []): Widget;
}
