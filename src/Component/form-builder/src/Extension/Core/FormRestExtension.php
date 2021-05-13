<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Extension\Core;

use Symfony\Component\Form\FormTypeInterface;
use Tulia\Component\FormBuilder\Extension\AbstractExtension;
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
        $builder
            ->add($this->id, [
                'label' => $this->label,
                'translation_domain' => $this->translationDomain,
                'priority' => -1000,
                'template' => <<<EOF
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="empty-form-section-placeholder" data-placeholder="{{ 'thereAreNoOtherSettings'|trans }}">{{ form_rest(form) }}</div>
        </div>
    </div>
</div>
EOF
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FormTypeInterface $formType, array $options, $data = null): bool
    {
        return true;
    }
}
