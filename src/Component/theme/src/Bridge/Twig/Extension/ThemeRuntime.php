<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Bridge\Twig\Extension;

use Requtize\Assetter\AssetterInterface;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Tulia\Component\Theme\ManagerInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeRuntime implements RuntimeExtensionInterface
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
     * @param mixed $default
     * @return mixed
     */
    public function customizerGet(string $name, $default = null)
    {
        return $this->manager->getTheme()->getConfig()->get('customizer', $name, $default);
    }

    public function customizerLiveControl(string $name, array $options = []): string
    {
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
    }

    public function themeHead(): string
    {
        return $this->assetter->build('head')->all();
    }

    public function themeBody(): string
    {
        return $this->assetter->build()->all();
    }
}
