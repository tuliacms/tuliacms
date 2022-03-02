<?php

declare(strict_types=1);

namespace Tulia\Cms\Widget\Infrastructure\Cms\Widget\Predefined\Features;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Widget\Domain\Catalog\AbstractWidget;
use Tulia\Cms\Widget\Domain\Catalog\Configuration\ConfigurationInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FeaturesWidget extends AbstractWidget
{
    public function configure(ConfigurationInterface $configuration): void
    {
        $configuration->multilingualFields(['features']);
        $configuration->set('features', [[
            'label' => '',
            'description' => '',
            'icon' => '',
            'position' => '0',
        ]]);
    }

    /**
     * {@inheritdoc}
     */
    public function render(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/features/frontend.tpl', [
            'features' => $config->get('features'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getView(ConfigurationInterface $config): ?ViewInterface
    {
        return $this->view('@widget/internal/features/backend.tpl');
    }

    public function saveAction(FormInterface $form, ConfigurationInterface $config): void
    {
        $features = array_values($config->get('features'));

        usort($features, function ($a, $b) {
            return ((int) $a['position']) - ((int) $b['position']);
        });

        $config->set('features', $features);
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(ConfigurationInterface $config): ?string
    {
        return FeaturesForm::class;
    }
}
