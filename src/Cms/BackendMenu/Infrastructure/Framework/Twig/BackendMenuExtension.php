<?php

declare(strict_types=1);

namespace Tulia\Cms\BackendMenu\Infrastructure\Framework\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Cms\BackendMenu\Application\HtmlBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BackendMenuExtension extends AbstractExtension
{
    /**
     * @var HtmlBuilderInterface
     */
    protected $builder;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param HtmlBuilderInterface $builder
     * @param RequestStack         $requestStack
     */
    public function __construct(HtmlBuilderInterface $builder, RequestStack $requestStack)
    {
        $this->builder      = $builder;
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
