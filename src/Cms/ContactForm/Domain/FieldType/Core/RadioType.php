<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType\Core;

/**
 * @author Adam Banaszkiewicz
 */
class RadioType extends SelectType
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'radio';
    }
}
