<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Settings;

use Tulia\Cms\ContentBuilder\Domain\ReadModel\Service\ContentTypeRegistryInterface;
use Tulia\Cms\Settings\Domain\Group\AbstractSettingsGroupFactory;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsFactory extends AbstractSettingsGroupFactory
{
    protected ContentTypeRegistryInterface $registry;

    public function __construct(ContentTypeRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function factory(): iterable
    {
        foreach($this->registry->allByType('node') as $type) {
            yield new SettingsGroup($type);
        }

        return [];
    }
}
