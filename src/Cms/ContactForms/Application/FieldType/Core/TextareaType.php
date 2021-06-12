<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Core;

use Tulia\Cms\ContactForms\Application\FieldType\AbstractType;

/**
 * @author Adam Banaszkiewicz
 */
class TextareaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'textarea';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\TextareaType::class;
    }
}
