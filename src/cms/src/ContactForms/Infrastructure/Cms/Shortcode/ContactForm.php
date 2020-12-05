<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\Infrastructure\Cms\Shortcode;

use Tulia\Component\Shortcode\Compiler\ShortcodeCompilerInterface;
use Tulia\Component\Shortcode\ShortcodeInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ContactForm implements ShortcodeCompilerInterface
{
    /**
     * {@inheritdoc}
     */
    public function compile(ShortcodeInterface $shortcode): string
    {
        return "{{ contact_form('{$shortcode->getParameter('id')}') }}";
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'contact_form';
    }
}
