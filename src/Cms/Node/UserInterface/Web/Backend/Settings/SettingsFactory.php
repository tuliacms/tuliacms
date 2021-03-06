<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Settings;

use Tulia\Cms\Node\Domain\NodeType\NodeTypeRegistryInterface;
use Tulia\Cms\Settings\Ports\Domain\Group\AbstractSettingsGroupFactory;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsFactory extends AbstractSettingsGroupFactory
{
    protected NodeTypeRegistryInterface $registry;

    public function __construct(NodeTypeRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function factory(): iterable
    {
        foreach($this->registry->all() as $type) {
            yield new SettingsGroup($type);
        }

        return [];
    }
}
