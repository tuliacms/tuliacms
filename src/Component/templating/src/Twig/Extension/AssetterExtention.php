<?php

declare(strict_types=1);

namespace Tulia\Component\Templating\Twig\Extension;

use Requtize\Assetter\AssetterInterface;
use Tulia\Component\Templating\Twig\Assetter\AssetterTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class AssetterExtention extends AbstractExtension implements GlobalsInterface
{
    protected AssetterInterface $assetter;

    protected string $basePath;

    public function __construct(AssetterInterface $assetter, string $basePath = '/')
    {
        $this->assetter = $assetter;
        $this->basePath = $basePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenParsers()
    {
        return [
            new AssetterTokenParser(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset', function (?string $path) {
                if ($this->basePath === '/') {
                    return $path;
                }

                return $this->basePath . $path;
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('assetter_standalone_assets', function (array $assets) {
                $standalone = $this->assetter->standalone();
                $standalone->require($assets);
                $build = $standalone->build();

               return [
                   'scripts' => $build->collectScripts(),
                   'stylesheets' => $build->collectStyles(),
               ];
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals(): array
    {
        return [
            '__assetter' => $this->assetter
        ];
    }
}
