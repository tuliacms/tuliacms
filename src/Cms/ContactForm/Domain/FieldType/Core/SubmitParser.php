<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType\Core;

use Tulia\Cms\ContactForm\Domain\FieldType\AbstractFieldParser;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class SubmitParser extends AbstractFieldParser
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'submit';
    }

    /**
     * {@inheritdoc}
     */
    public function parseShortcode(ShortcodeInterface $shortcode): array
    {
        return [
            'name' => $shortcode->getParameter('name', 'submit'),
            'type' => SubmitType::class,
            'options' => [
                'label' => $shortcode->getParameter('label'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): array
    {
        return [
            'name' => 'Submit button',
            'options' => [
                'name' => [
                    'name' => 'Field codename. Must be unique across whole form.',
                    'type' => 'text',
                    'required' => true,
                ],
                'label' => [
                    'name' => 'Label showed in field form.',
                    'type' => 'text',
                    'required' => true,
                    'multilingual' => true,
                ],
            ],
        ];
    }
}
