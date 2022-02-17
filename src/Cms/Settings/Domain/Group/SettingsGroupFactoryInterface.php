<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Domain\Group;

/**
 * @author Adam Banaszkiewicz
 */
interface SettingsGroupFactoryInterface
{
    public function factory(): iterable;

    public function doFactory(): iterable;
}
