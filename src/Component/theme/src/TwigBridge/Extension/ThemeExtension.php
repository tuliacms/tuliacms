<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\TwigBridge\Extension;

use Tulia\Component\Theme\ManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    /**
     * @param ManagerInterface $manager
     */
    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
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
        ];
    }
}
