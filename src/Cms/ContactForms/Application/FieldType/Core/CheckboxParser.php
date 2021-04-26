<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Core;

use Tulia\Cms\ContactForms\Application\FieldType\Parser\AbstractFieldParser;
use Tulia\Component\Shortcode\ShortcodeInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Adam Banaszkiewicz
 */
class CheckboxParser extends AbstractFieldParser
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'checkbox';
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
            'type' => CheckboxType::class,
            'options' => [
                'constraints_raw' => $constraintsRaw,
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
            'name' => 'Email',
            'options' => [
                'name' => [
                    'name' => 'Field codename.',
                    'type' => 'text',
                    'required' => true,
                ],
                'label' => [
                    'name' => 'Label showed in field form.',
                    'type' => 'text',
                    'required' => true,
                ],
                'constraints' => [
                    'name' => 'Validation constraints for this field.',
                    'type' => 'collection',
                    'required' => false,
                    'collection' => [
                        'required' => 'Makes field required to submit form.',
                    ],
                ],
            ],
        ];
    }
}
