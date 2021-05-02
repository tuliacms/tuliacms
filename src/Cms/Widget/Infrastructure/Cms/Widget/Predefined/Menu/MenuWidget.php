<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Menu;

use Tulia\Cms\Menu\Infrastructure\Builder\BuilderInterface;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Widget\AbstractWidget;
use Tulia\Component\Widget\Configuration\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 * @todo Move widget to Menu module.
 */
class MenuWidget extends AbstractWidget
{
    protected BuilderInterface $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
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
    public function getId(): string
    {
        return 'internal.menu';
    }

    /**
     * {@inheritdoc}
     */
    public function render(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('frontend.tpl', [
            'menu' => $this->builder->buildHtml($config->get('menu_id')),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getView(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('backend.tpl');
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(ConfigurationInterface $config): ?string
    {
        return MenuForm::class;
    }
}
