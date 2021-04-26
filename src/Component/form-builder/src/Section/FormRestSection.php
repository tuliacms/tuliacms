<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Section;

/**
 * @author Adam Banaszkiewicz
 */
class FormRestSection extends AbstractSection
{
    /**
     * @param string $id
     * @param string|null $label
     * @param string|null $translationDomain
     */
    public function __construct(
        string $id,
        string $label = null,
        string $translationDomain = null
    ) {
        $this->setId($id);
        $this->setLabel($label);
        $this->setView(<<<EOF
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="empty-form-section-placeholder" data-placeholder="{{ 'thereAreNoOtherSettings'|trans }}">{{ form_rest(form) }}</div>
        </div>
    </div>
</div>
EOF
        );
        $this->setTranslationDomain($translationDomain);
    }
}
