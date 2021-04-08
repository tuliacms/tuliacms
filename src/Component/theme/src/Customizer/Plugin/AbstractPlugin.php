<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Plugin;

use Tulia\Component\Theme\ThemeInterface;
use Tulia\Component\Theme\Customizer\Changeset\ChangesetInterface;
use Tulia\Component\Theme\Customizer\Builder\BuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
abstract class AbstractPlugin implements PluginInterface
{
    public function beforeChangesetSave(ChangesetInterface $changeset)
    {

    }

    public function afterChangesetSave(ChangesetInterface $changeset)
    {

    }

    public function beforeChangesetRemove(ChangesetInterface $changeset)
    {

    }

    public function afterChangesetRemove(ChangesetInterface $changeset)
    {

    }

    public function setThemeChangeset(ChangesetInterface $changeset, ThemeInterface $theme)
    {

    }

    public function getBuilderForTheme(BuilderInterface $builder, ThemeInterface $theme)
    {

    }
}
