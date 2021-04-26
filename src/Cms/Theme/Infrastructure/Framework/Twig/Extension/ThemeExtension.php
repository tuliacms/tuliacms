<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Framework\Twig\Extension;

use Requtize\Assetter\AssetterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class ThemeExtension extends AbstractExtension
{
    private AssetterInterface $assetter;

    public function __construct(AssetterInterface $assetter)
    {
        $this->assetter = $assetter;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
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
