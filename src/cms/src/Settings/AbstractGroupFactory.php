<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractGroupFactory implements GroupFactoryInterface
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
