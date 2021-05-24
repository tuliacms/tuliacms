<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Infrastructure\Framework\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\BackendMenu\Domain\Builder\HtmlBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BackendMenuExtension extends AbstractExtension
{
    protected HtmlBuilderInterface $builder;

    protected RequestStack $requestStack;

    public function __construct(HtmlBuilderInterface $builder, RequestStack $requestStack)
    {
        $this->builder = $builder;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('backend_menu', function () {
                $ids = [];

                if ($this->requestStack->getMasterRequest()) {
                    $cookie = $this->requestStack->getMasterRequest()->cookies->get('aside-menuitems-opened');
                    $ids = $cookie ? explode('|', $cookie) : [];
                }

                return $this->builder->build([
                    'opened' => $ids
                ]);
            }, [
                'is_safe' => ['html']
            ])
        ];
    }
}
