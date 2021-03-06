<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForm\Domain\Shortcode;

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
        return sprintf("{{ contact_form('%s') }}", $shortcode->getParameter('id'));
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'contact_form';
    }
}
