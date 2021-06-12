<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Core;

use Tulia\Component\Shortcode\ShortcodeInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Adam Banaszkiewicz
 */
class RadioParser extends SelectParser
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'radio';
    }

    /**
     * {@inheritdoc}
     */
    public function parseShortcode(ShortcodeInterface $shortcode): array
    {
        $data = parent::parseShortcode($shortcode);
        $data['options']['expanded'] = true;
        $data['type'] = RadioType::class;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): array
    {
        $definition = parent::getDefinition();
        $definition['name'] = 'Radio';

        return $definition;
    }
}
