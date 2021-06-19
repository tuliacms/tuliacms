<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\Ports\Domain\Group;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractSettingsGroupFactory implements SettingsGroupFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    abstract public function factory(): iterable;

    /**
     * {@inheritdoc}
     */
    public function doFactory(): iterable
    {
        foreach($this->factory() as $group)
        {
            yield $group;
        }
    }
}
