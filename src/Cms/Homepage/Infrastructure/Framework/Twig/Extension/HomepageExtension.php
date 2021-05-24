<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\Infrastructure\Framework\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class HomepageExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_homepage', function ($context) {
                return $context['app']->getRequest()->getContentPath() === '/';
            }, [
                'is_safe' => [ 'html' ],
                'needs_context' => true,
            ]),
        ];
    }
}
