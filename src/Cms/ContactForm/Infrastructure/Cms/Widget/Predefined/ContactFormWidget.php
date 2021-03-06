<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Infrastructure\Cms\Widget\Predefined;

use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Widget\AbstractWidget;
use Tulia\Component\Widget\Configuration\ConfigurationInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContactFormWidget extends AbstractWidget
{
    public function configure(ConfigurationInterface $configuration): void
    {
        $configuration->set('form_id', null);
    }

    /**
     * {@inheritdoc}
     */
    public function getInfo(): array
    {
        return [
            'name' => 'widget.name',
            'description' => 'widget.description',
            'translation_domain' => 'contact-form',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getId(): string
    {
        return 'internal.contact-form';
    }

    /**
     * {@inheritdoc}
     */
    public function render(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/contact-form/frontend.tpl', [
            'form_id' => $config->get('form_id'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getView(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/contact-form/backend.tpl');
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(ConfigurationInterface $config): ?string
    {
        return ContactFormForm::class;
    }
}
