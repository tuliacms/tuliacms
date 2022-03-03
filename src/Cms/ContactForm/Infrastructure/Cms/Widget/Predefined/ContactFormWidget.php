<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Cms\Widget\Predefined;

use Tulia\Cms\Widget\Domain\Catalog\AbstractWidget;
use Tulia\Cms\Widget\Domain\Catalog\Configuration\ConfigurationInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContactFormWidget extends AbstractWidget
{
    public function configure(ConfigurationInterface $configuration): void
    {
        $configuration->set('form_id', null);
    }

    public function render(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/contact_form/frontend.tpl', [
            'form_id' => $config->get('form_id'),
        ]);
    }

    public function getView(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/contact_form/backend.tpl');
    }
}
