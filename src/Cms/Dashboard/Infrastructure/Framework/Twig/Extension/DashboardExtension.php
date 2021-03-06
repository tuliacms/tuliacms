<?php

declare(strict_types=1);

namespace Tulia\Cms\Dashboard\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Dashboard\Domain\Widgets\DashboardWidgetRegistry;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class DashboardExtension extends AbstractExtension
{
    protected DashboardWidgetRegistry $widgetsRegistry;

    public function __construct(DashboardWidgetRegistry $widgetsRegistry)
    {
        $this->widgetsRegistry = $widgetsRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('dashboard_widgets', function (string $group) {
                $result = '';

                foreach ($this->widgetsRegistry->allSupporting($group) as $widget) {
                    $result .= $widget->render();
                }

                return $result;
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
