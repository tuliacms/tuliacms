<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Extension\Core;

use Symfony\Component\Form\FormTypeInterface;
use Tulia\Component\FormBuilder\Extension\AbstractExtension;
use Tulia\Component\FormBuilder\Section\FormRestSection;
use Tulia\Component\FormBuilder\Section\SectionsBuilderInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FormRestExtension extends AbstractExtension
{
    protected string $id;

    protected string $label;

    protected string $translationDomain;

    public function __construct(string $id = null, string $label = null, string $translationDomain = null)
    {
        $this->id = $id ?? 'form-rest';
        $this->label = $label ?? 'otherSettings';
        $this->translationDomain = $translationDomain ?? 'messages';
    }

    /**
     * {@inheritdoc}
     */
    public function getSections(SectionsBuilderInterface $builder): void
    {
        $builder->add(new FormRestSection($this->id, $this->label, $this->translationDomain))
            ->setPriority(-1000);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return true;
    }
}
