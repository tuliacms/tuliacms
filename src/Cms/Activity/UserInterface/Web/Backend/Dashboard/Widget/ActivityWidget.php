<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\UserInterface\Web\Backend\Dashboard\Widget;

use Tulia\Cms\Activity\Domain\ReadModel\ActivityFinder;
use Tulia\Cms\Dashboard\Widgets\AbstractWidget;

/**
 * @author Adam Banaszkiewicz
 */
class ActivityWidget extends AbstractWidget
{
    private ActivityFinder $finder;

    public function __construct(ActivityFinder $finder)
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
