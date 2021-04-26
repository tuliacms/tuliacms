<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Plugin;

use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Component\Theme\Customizer\Builder\Section\SectionInterface;
use Tulia\Component\Theme\Customizer\Builder\Controls\ControlInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TranslatorPlugin extends AbstractPlugin
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function addSection(SectionInterface $section): void
    {
        if ($section->get('translation_domain') !== false) {
            $section->set('label', $this->translator->trans($section->get('label'), [], $section->get('translation_domain')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addControl(ControlInterface $control): void
    {
        if ($control->get('translation_domain') !== false) {
            $control->set('label', $this->translator->trans($control->get('label'), [], $control->get('translation_domain')));
        }
    }
}
