<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Resolver;

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Component\Theme\Customizer\Changeset\Storage\StorageInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\Enum\ChangesetTypeEnum;
use Tulia\Component\Theme\ThemeInterface;
use Tulia\Component\Theme\ManagerInterface;
use Tulia\Component\Theme\Customizer\CustomizerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class CustomizerResolver implements ResolverInterface
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @var CustomizerInterface
     */
    protected $customizer;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var DetectorInterface
     */
    protected $detector;

    /**
     * @param ManagerInterface $manager
     * @param CustomizerInterface $customizer
     * @param DetectorInterface $detector
     */
    public function __construct(
        ManagerInterface $manager,
        CustomizerInterface $customizer,
        StorageInterface $storage,
        DetectorInterface $detector
    ) {
        $this->manager    = $manager;
        $this->customizer = $customizer;
        $this->storage    = $storage;
        $this->detector   = $detector;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(ThemeInterface $theme): void
    {
        if ($theme->hasConfig() === false) {
            return;
        }

        $config    = $theme->getConfig();
        $changeset = $this->storage->getActiveChangeset($theme->getName());

        /**
         * If changeset not found, we create default one, and saves it
         * in storage for future usages. Saving default values in Changeset
         * prevents do the same operation (building with defaults)
         * in every request when new/fresh theme installed.
         */
        if (!$changeset) {
            $changeset = $this->customizer->buildDefaultChangeset($theme);
            $changeset->setType(ChangesetTypeEnum::ACTIVE);
            $this->storage->save($changeset);
        }

        foreach ($changeset as $key => $val) {
            $config->add('customizer', $key, $val);
        }

        if ($this->detector->isCustomizerMode() === false) {
            return;
        }

        $id = $this->detector->getChangesetId();

        if ($this->storage->has($id)) {
            $changeset = $this->storage->get($id);

            foreach ($changeset as $key => $val) {
                $config->add('customizer', $key, $val);
            }
        }
    }
}
