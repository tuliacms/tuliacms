<?php

declare(strict_types=1);

namespace Tulia\Cms\Node\UserInterface\Web\Backend\Settings;

use Tulia\Cms\ContentBuilder\Domain\ContentType\Service\ContentTypeRegistry;
use Tulia\Cms\Settings\Ports\Domain\Group\AbstractSettingsGroupFactory;

/**
 * @author Adam Banaszkiewicz
 */
class SettingsFactory extends AbstractSettingsGroupFactory
{
    protected ContentTypeRegistry $registry;

    public function __construct(ContentTypeRegistry $registry)
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
