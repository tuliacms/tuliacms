<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\Infrastructure\Cms\Settings;

use Tulia\Cms\Node\Infrastructure\NodeType\RegistryInterface;
use Tulia\Cms\Settings\AbstractGroupFactory;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsFactory extends AbstractGroupFactory
{
    /**
     * @var RegistryInterface
     */
    protected $registry;

    /**
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function factory(): iterable
    {
        foreach($this->registry->all() as $type)
        {
            yield new SettingsGroup($type);
        }

        return [];
    }
}
