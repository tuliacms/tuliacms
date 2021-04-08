<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\Infrastructure\Cms\Dashboard\Widget;

use Tulia\Cms\Activity\Query\FinderInterface;
use Tulia\Cms\Dashboard\Widgets\AbstractWidget;

/**
 * @author Adam Banaszkiewicz
 */
class ActivityWidget extends AbstractWidget
{
    private $finder;

    public function __construct(FinderInterface $finder)
    {
        $this->finder = $finder;
    }

    public function render(): string
    {
        return $this->view('@backend/activity/activity-widget.tpl', [
            'rows' => $this->finder->findPart(),
        ]);
    }

    public function supports(string $group): bool
    {
        return $group === 'backend.dashboard';
    }
}
