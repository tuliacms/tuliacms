<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Framework\Twig\Extension;

use Tulia\Cms\Menu\Infrastructure\Builder\BuilderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class MenuExtension extends AbstractExtension
{
    /**
     * @var BuilderInterface
     */
    protected $builder;

    /**
     * @param BuilderInterface $builder
     */
    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('show_menu', function (string $id) {
                return $this->builder->buildHtml($id);
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }
}
