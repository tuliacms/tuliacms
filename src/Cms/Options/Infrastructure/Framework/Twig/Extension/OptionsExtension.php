<?php

declare(strict_types=1);

namespace Tulia\Cms\Options\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Options\Application\Service\Options;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class OptionsExtension extends AbstractExtension
{
    /**
     * @var Options
     */
    protected $options;

    /**
     * @param Options $options
     */
    public function __construct(Options $options)
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
