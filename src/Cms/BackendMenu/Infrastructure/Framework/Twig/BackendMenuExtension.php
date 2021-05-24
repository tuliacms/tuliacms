<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Infrastructure\Framework\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Tulia\Cms\BackendMenu\Domain\Builder\HtmlBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BackendMenuExtension extends AbstractExtension
{
    protected HtmlBuilderInterface $builder;

    public function __construct(HtmlBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('backend_menu', function ($context) {
                $cookie = $context['app']->getRequest()->cookies->get('aside-menuitems-opened');
                $ids = $cookie ? explode('|', $cookie) : [];

                return $this->builder->build([
                    'opened' => $ids
                ]);
            }, [
                'is_safe' => ['html'],
                'needs_context' => true,
            ])
        ];
    }
}
