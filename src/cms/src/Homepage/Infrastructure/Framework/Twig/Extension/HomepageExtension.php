<?php

declare(strict_types=1);

namespace Tulia\Cms\Homepage\Infrastructure\Framework\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class HomepageExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('is_homepage', function () {
                return $this->requestStack->getMasterRequest()->getContentPath() === '/';
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
