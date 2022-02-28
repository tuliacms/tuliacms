<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Resolver;

use Tulia\Component\Theme\Configuration\ConfigurationInterface;
use Tulia\Component\Theme\Customizer\Changeset\DefaultChangesetFactory;
use Tulia\Component\Theme\Customizer\Changeset\Storage\StorageInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\Enum\ChangesetTypeEnum;
use Tulia\Component\Theme\ThemeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CustomizerResolver implements ResolverInterface
{
    private DefaultChangesetFactory $defaultChangesetFactory;
    private StorageInterface $storage;
    private DetectorInterface $detector;

    public function __construct(
        DefaultChangesetFactory $defaultChangesetFactory,
        StorageInterface $storage,
        DetectorInterface $detector
    ) {
        $this->defaultChangesetFactory = $defaultChangesetFactory;
        $this->storage = $storage;
        $this->detector = $detector;
    }

    public function resolve(ThemeInterface $theme): void
    {
        if ($theme->hasConfig() === false) {
            return;
        }

        $config    = $theme->getConfig();
        $changeset = $this->storage->getActiveChangeset($theme->getName());

        if (! $changeset) {
            $changeset = $this->defaultChangesetFactory->buildForTheme($theme);
            $changeset->setType(ChangesetTypeEnum::ACTIVE);
        }

        foreach ($changeset as $key => $val) {
            $config->add('customizer', $key, $val);
        }

        if ($this->detector->isCustomizerMode()) {
            $this->applyCustomizerAwareChangeset($config);
        }
    }

    private function applyCustomizerAwareChangeset(ConfigurationInterface $config): void
    {
        $id = $this->detector->getChangesetId();

        if ($this->storage->has($id)) {
            $changeset = $this->storage->get($id);

            foreach ($changeset as $key => $val) {
                $config->add('customizer', $key, $val);
            }
        }
    }
}
