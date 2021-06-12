<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Core;

use Tulia\Cms\ContactForms\Application\FieldType\AbstractType;

/**
 * @author Adam Banaszkiewicz
 */
class SelectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'select';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareValueFromRequest($value, array $options)
    {
        $choices = array_flip($options['choices']);

        return $choices[$value] ?? $value;
    }
}
