<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\DefaultTheme;

use Tulia\Cms\Platform\Version;
use Tulia\Component\Theme\AbstractTheme;

/**
 * @author Adam Banaszkiewicz
 */
class DefaultTheme extends AbstractTheme
{
    protected $version = Version::VERSION;
    protected $name    = 'DefaultTheme';
    protected $author  = 'Adam Banaszkiewicz';
    protected $info    = 'Default Theme for Tulia CMS.';

    /**
     * {@inheritdoc}
     */
    public function getDirectory(): string
    {
        return __DIR__;
    }
}
