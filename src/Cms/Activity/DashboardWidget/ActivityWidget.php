<?php

declare(strict_types=1);

namespace Tulia\Cms\Activity\DashboardWidget;

use Tulia\Cms\Activity\Model\ActivityStorageInterface;
use Tulia\Cms\Homepage\UserInterface\Web\Backend\Widgets\AbstractDashboardWidget;

/**
 * @author Adam Banaszkiewicz
 */
class ActivityWidget extends AbstractDashboardWidget
{
    private ActivityStorageInterface $storage;

    public function __construct(ActivityStorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function render(): string
    {
        return $this->view('@backend/activity/activity-widget.tpl', [
            'rows' => $this->storage->findCollection(),
        ]);
    }

    public function supports(string $group): bool
    {
        return $group === 'backend.dashboard';
    }
}
