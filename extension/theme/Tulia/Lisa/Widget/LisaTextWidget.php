<?php

declare(strict_types = 1);

namespace Tulia\Theme\Tulia\Lisa\Widget;

use Symfony\Component\Form\FormInterface;
use Tulia\Cms\Widget\Domain\Catalog\AbstractWidget;
use Tulia\Cms\Widget\Domain\Catalog\Configuration\ConfigurationInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class LisaTextWidget extends AbstractWidget
{
    /**
     * {@inheritdoc}
     */
    public function getInfo(): array
    {
        return [
            'name' => 'Lisa Theme - Text widget',
            'description' => 'Lisa Theme - Text widget',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getId(): string
    {
        return 'lisa-text';
    }

    /**
     * {@inheritdoc}
     */
    public function renderFrontAction(ConfigurationInterface $config): ViewInterface
    {
        dump('Frontend :)');
    }

    /**
     * {@inheritdoc}
     */
    public function renderAdminAction(ConfigurationInterface $config): ViewInterface
    {
        dump('Backend :)');
    }

    /**
     * {@inheritdoc}
     */
    public function saveAction(FormInterface $form, ConfigurationInterface $config): void
    {
        dump('Save :)');
        exit;
    }

    public function render(ConfigurationInterface $config): ?ViewInterface
    {
        // TODO: Implement render() method.
    }
}
