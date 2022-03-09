<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\TwigBridge\Extension;

use Requtize\Assetter\AssetterInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\ManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'customizer_get',
                [ThemeRuntime::class, 'customizerGet'],
                ['is_safe' => [ 'html' ]]
            ),
            new TwigFunction(
                'customizer_live_control',
                [ThemeRuntime::class, 'customizerLiveControl'],
                ['is_safe' => [ 'html' ]]
            ),
            new TwigFunction(
                'theme_head',
                [ThemeRuntime::class, 'themeHead'],
                ['is_safe' => [ 'html' ]]
            ),
            new TwigFunction(
                'theme_body',
                [ThemeRuntime::class, 'themeBody'],
                ['is_safe' => [ 'html' ]]
            ),
        ];
    }
}
