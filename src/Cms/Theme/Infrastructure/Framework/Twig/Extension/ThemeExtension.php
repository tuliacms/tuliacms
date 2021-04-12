<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Twig\Extension;

use Tulia\Component\Hooking\HookerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeExtension extends AbstractExtension
{
    private HookerInterface $hooker;

    /*public function __construct(HookerInterface $hooker)
    {
        $this->hooker = $hooker;
    }*/

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('theme_head', function () {
                //return $this->hooker->doAction('theme-head');
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('theme_body', function () {
                //return $this->hooker->doAction('theme-body');
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
