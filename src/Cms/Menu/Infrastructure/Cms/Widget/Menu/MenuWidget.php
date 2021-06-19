<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Cms\Widget\Menu;

use Tulia\Cms\Menu\Domain\Builder\BuilderInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Widget\AbstractWidget;
use Tulia\Component\Widget\Configuration\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
class MenuWidget extends AbstractWidget
{
    protected BuilderInterface $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public static function getId(): string
    {
        return 'internal.menu';
    }

    public function configure(ConfigurationInterface $configuration): void
    {
        $configuration->multilingualFields([]);
        $configuration->set('menu_id', null);
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(): array
    {
        return [
            'name' => 'widget.menu.name',
            'description' => 'widget.menu.description',
            'translation_domain' => 'widgets',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function render(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/menu/frontend.tpl', [
            'menu' => $this->builder->buildHtml($config->get('menu_id')),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getView(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/menu/backend.tpl');
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(ConfigurationInterface $config): ?string
    {
        return MenuForm::class;
    }
}
