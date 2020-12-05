<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Text;

use Tulia\Component\Templating\ViewInterface;
use Tulia\Component\Widget\AbstractWidget;
use Tulia\Component\Widget\Configuration\ConfigurationInterface;

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

    /**
     * {@inheritdoc}
     */
    public function getInfo(): array
    {
        return [
            'name' => 'widget.text.name',
            'description' => 'widget.text.description',
            'translation_domain' => 'widgets',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): string
    {
        return 'internal.text';
    }

    /**
     * {@inheritdoc}
     */
    public function render(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('frontend.tpl', [
            'content' => $config->get('content'),
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
        return TextForm::class;
    }
}
