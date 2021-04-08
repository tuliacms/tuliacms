<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Section;

/**
 * @author Adam Banaszkiewicz
 */
class FormRowSection extends AbstractSection
{
    /**
     * @param string $id
     * @param string|null $label
     * @param string|array|null $field
     * @param string|null $translationDomain
     */
    public function __construct(
        string $id,
        string $label = null,
        $field = null,
        string $translationDomain = null
    ) {
        if (!$field) {
            $field = $id;
        }

        if (\is_string($field)) {
            $field = [$field];
        }

        if (\is_array($field)) {
            $this->setFields($field);
            $this->setView(implode('', array_map(function ($item) {
                return '{{ form_row(form.' . $item . ') }}';
            }, $field)));
        }

        $this->setId($id);
        $this->setLabel($label);
        $this->setTranslationDomain($translationDomain);
    }
}
