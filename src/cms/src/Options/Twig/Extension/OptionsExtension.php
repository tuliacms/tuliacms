<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Twig\Extension;

use Tulia\Cms\Options\OptionsInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class OptionsExtension extends AbstractExtension
{
    /**
     * @var OptionsInterface
     */
    protected $options;

    /**
     * @param OptionsInterface $options
     */
    public function __construct(OptionsInterface $options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('option', function (string $name, $default = null) {
                return $this->options->get($name, $default);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
