<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Builder;

use Tulia\Component\FormBuilder\Section\SectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BootstrapAccordionGroupBuilder extends AbstractGroupBuilder
{
    /**
     * @var string
     */
    protected $group;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string
     */
    protected static $templateDefault = <<<EOF
<div class="accordion-section">
    <div class="accordion-section-button{sectionActiveTab}" data-toggle="collapse" data-target="#form-collapse-{sectionId}">
        {{ '{sectionLabel}'|trans({}, '{sectionTranslationDomain}') }}
    </div>
    <div id="form-collapse-{sectionId}" class="collapse{sectionActiveContent}">
        <div class="accordion-section-body">
            {sectionView}
        </div>
    </div>
</div>
EOF
    ;

    /**
     * @param string $group
     * @param string|null $template
     */
    public function __construct(string $group = 'sidebar', string $template = null)
    {
        $this->group    = $group;
        $this->template = empty($template) ? static::$templateDefault : $template;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $sections): string
    {
        $result = '';

        /** @var SectionInterface $section */
        foreach ($sections as $section) {
            $replacements = $this->getReplacements($section);

            $result .= str_replace(
                array_keys($replacements),
                array_values($replacements),
                $this->getTemplate()
            );
        }

        return '<div class="accordion">' . $result . '</div>';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsGroup(?string $group): bool
    {
        return $this->group === $group;
    }

    public function getReplacements(SectionInterface $section): array
    {
        return [
            '{sectionId}' => $section->getId(),
            '{sectionLabel}' => $section->getLabel(),
            '{sectionTranslationDomain}' => $section->getTranslationDomain() ?? 'messages',
            '{sectionFields}' => $section->getFieldsStatement(),
            '{sectionView}' => $section->getViewStatement(),
            '{sectionActiveTab}' => $this->isSectionActive($section->getId()) ? '' : ' collapsed',
            '{sectionActiveContent}' => $this->isSectionActive($section->getId()) ? ' show' : '',
        ];
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}
