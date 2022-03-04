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
class ThemeExtension extends AbstractExtension implements GlobalsInterface
{
    private ManagerInterface $manager;
    private AssetterInterface $assetter;
    private DetectorInterface $detector;

    public function __construct(
        ManagerInterface $manager,
        AssetterInterface $assetter,
        DetectorInterface $detector
    ) {
        $this->manager = $manager;
        $this->assetter = $assetter;
        $this->detector = $detector;
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
            new TwigFunction('customizer_live_control', function (string $name, array $options = []) {
                if ($this->detector->isCustomizerMode()) {
                    $options = array_merge([
                        'hide_empty' => true,
                        'nl2br' => false,
                        'type' => 'inner-text',
                        'default' => null,
                    ], $options);

                    return ' data-tulia-customizer-live-control=\'{"control":"'.$name.'","nl2br":"'.($options['nl2br'] ? 'true' : 'false').'","type":"'.$options['type'].'","default":"'.$options['default'].'"}\'';
                }

                return '';
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
