<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Text;

use Tulia\Cms\Widget\Domain\Catalog\AbstractWidget;
use Tulia\Cms\Widget\Domain\Catalog\Configuration\ConfigurationInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TextWidget extends AbstractWidget
{
    public function configure(ConfigurationInterface $configuration): void
    {
        $configuration->multilingualFields(['content']);
        $configuration->set('content', '');
    }

    public function render(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/text/frontend.tpl', [
            'content' => $config->get('content'),
        ]);
    }

    public function getView(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/text/backend.tpl');
    }
}
