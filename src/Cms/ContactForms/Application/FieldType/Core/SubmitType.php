<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Application\FieldType\Core;

use Tulia\Cms\ContactForms\Application\FieldType\AbstractType;

/**
 * @author Adam Banaszkiewicz
 */
class SubmitType extends AbstractType
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
    public function getFormType(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\SubmitType::class;
    }
}
