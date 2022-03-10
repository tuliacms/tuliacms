<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\ViewFilter;

use Tulia\Component\Templating\Config;
use Tulia\Component\Theme\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ViewNamespaceOverwriteFilter implements FilterInterface
{
    private Config $config;
    private ManagerInterface $manager;
    private array $workingMap = [];
    private array $views = [];

    public function __construct(Config $config, ManagerInterface $manager)
    {
        $this->config = $config;
        $this->manager = $manager;
    }

    public function filter(string $view): array
    {
        $this->workingMap = $this->config->get('namespace_overwrite');

        // Skip views without namespace
        if (strncmp($view, '@', 1) !== 0) {
            return [$view];
        }

        $this->views = [$view];

        while ($this->loop()) {
            // Empty while, to loop through all $config ocurrencies.
        }

        $this->views = $this->replaceThemePrefixes($this->views);

        return array_reverse($this->views);
    }

    private function loop(): bool
    {
        foreach ($this->views as $view) {
            foreach ($this->workingMap as $key => $map) {
                if (strpos($view, $map['from']) === 0) {
                    $overwritten = str_replace($map['from'], $map['to'], $view);

                    $this->views[] = $overwritten;

                    // Unset used to prevent infinite loop.
                    unset($this->workingMap[$key]);

                    return true;
                }
            }
        }

        return false;
    }

    private function replaceThemePrefixes(array $views): array
    {
        $theme = $this->manager->getTheme();
        $themeName = $theme->getName();
        $parentThemeName = $theme->getParent();

        foreach ($views as $key => $view) {
            if (strncmp($view, '@theme/', 7) === 0) {
                $views[$key] = str_replace('@theme/', "@$themeName/", $view);
            }
            if ($parentThemeName && strncmp($view, '@parent/', 8) === 0) {
                $views[$key] = str_replace('@parent/', "@$parentThemeName/", $view);
            }
        }

        return $views;
    }
}
