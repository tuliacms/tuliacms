<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Section;

/**
 * @author Adam Banaszkiewicz
 */
class Section extends AbstractSection
{
    /**
     * @param string $id
     * @param string|null $label
     * @param string|null $view
     * @param string|null $translationDomain
     */
    public function __construct(
        string $id,
        string $label = null,
        string $view = null,
        string $translationDomain = null
    ) {
        $this->setId($id);
        $this->setLabel($label);
        $this->setView($view);
        $this->setTranslationDomain($translationDomain);
    }
}
