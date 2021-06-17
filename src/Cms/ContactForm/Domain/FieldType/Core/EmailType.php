<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\FieldType\Core;

use Symfony\Component\Validator\Constraints\Email;
use Tulia\Cms\ContactForm\Ports\Domain\FieldType\AbstractFieldType;

/**
 * @author Adam Banaszkiewicz
 */
class EmailType extends AbstractFieldType
{
    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'email';
    }

    /**
     * {@inheritdoc}
     */
    public function getFormType(): string
    {
        return \Symfony\Component\Form\Extension\Core\Type\EmailType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptions(array $options): array
    {
        /**
         * By default, add Email constraint for all Email field types.
         */
        $options['constraints'][] = new Email();

        // Remove special option, used by sender.
        unset($options['sender']);

        return $options;
    }
}
