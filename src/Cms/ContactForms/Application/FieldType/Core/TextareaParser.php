<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Core;

use Tulia\Cms\ContactForms\Application\FieldType\Parser\AbstractFieldParser;
use Tulia\Component\Shortcode\ShortcodeInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Adam Banaszkiewicz
 */
class TextareaParser extends AbstractFieldParser
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function parseShortcode(ShortcodeInterface $shortcode): array
    {
        $constraints = $this->parseConstraints($shortcode->getParameter('constraints'));
        $constraintsRaw = [];

        if (isset($constraints['required'])) {
            $constraintsRaw[] = [
                'name' => NotBlank::class,
            ];
        }

        return [
            'name' => $shortcode->getParameter('name'),
            'type' => TextareaType::class,
            'options' => [
                'constraints_raw' => $constraintsRaw,
                'label' => $shortcode->getParameter('label'),
                'help' => $shortcode->getParameter('help'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition(): array
    {
        return [
            'name' => 'Textarea',
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
                ],
                'help' => [
                    'name' => 'Help text showed under the field.',
                    'type' => 'text',
                    'required' => false,
                ],
                'constraints' => [
                    'name' => 'Validation constraints for this field.',
                    'type' => 'collection',
                    'required' => false,
                    'collection' => [
                        'required' => 'Makes field required to submit form.',
                        //'max:50' => 'Requires maximum length of text in this field to specified number.',
                    ],
                ],
            ],
        ];
    }
}
