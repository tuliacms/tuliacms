<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Domain\FieldType\Core;

use Tulia\Cms\ContactForms\Ports\Domain\FieldType\AbstractFieldType;

/**
 * @author Adam Banaszkiewicz
 */
class SubmitType extends AbstractFieldType
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
