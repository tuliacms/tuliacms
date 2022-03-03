<?php

declare(strict_types=1);

namespace Tulia\Cms\Menu\Infrastructure\Cms\Widget\Menu;

use Tulia\Cms\Menu\Domain\Builder\BuilderInterface;
use Tulia\Cms\Widget\Domain\Catalog\AbstractWidget;
use Tulia\Cms\Widget\Domain\Catalog\Configuration\ConfigurationInterface;
use Tulia\Component\Templating\ViewInterface;

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

    public function configure(ConfigurationInterface $configuration): void
    {
        $configuration->multilingualFields([]);
        $configuration->set('menu_id', null);
    }

    public function render(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/menu/frontend.tpl', [
            'menu' => $this->builder->buildHtml((string) $config->get('menu_id')),
        ]);
    }

    public function getView(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/menu/backend.tpl');
    }
}
