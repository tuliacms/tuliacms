<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\ViewFilter;

use Tulia\Component\Templating\Config;

/**
 * @author Adam Banaszkiewicz
 */
class ViewNamespaceOverwriteFilter implements FilterInterface
{
    private Config $config;
    private array $workingMap = [];
    private array $views = [];

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
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
}
