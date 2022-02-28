<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\TwigBridge\Extension;

use Requtize\Assetter\AssetterInterface;
use Tulia\Component\Theme\ManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeExtension extends AbstractExtension implements GlobalsInterface
{
    private ManagerInterface $manager;
    private AssetterInterface $assetter;

    public function __construct(ManagerInterface $manager, AssetterInterface $assetter)
    {
        $this->manager = $manager;
        $this->assetter = $assetter;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals(): array
    {
        return [
            'theme' => $this->manager->getTheme(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('customizer_get', function (string $name, $default = null) {
                return $this->manager->getTheme()->getConfig()->get('customizer', $name, $default);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('theme_head', function () {
                return $this->assetter->build('head')->all();
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('theme_body', function () {
                return $this->assetter->build()->all();
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
