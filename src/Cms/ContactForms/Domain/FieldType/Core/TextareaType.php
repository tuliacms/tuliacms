<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\FieldType\Core;

use Tulia\Cms\ContactForms\Ports\Domain\FieldType\AbstractFieldType;

/**
 * @author Adam Banaszkiewicz
 */
class TextareaType extends AbstractFieldType
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
