<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings;

/**
 * @author Adam Banaszkiewicz
 */
interface GroupFactoryInterface
{
    /**
     * @return iterable
     */
    public function factory(): iterable;

    /**
     * @return iterable
     */
    public function doFactory(): iterable;
}
