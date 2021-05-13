<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Builder;

/**
 * @author Adam Banaszkiewicz
 */
class BootstrapAccordionGroupBuilder extends AbstractGroupBuilder
{
    protected string $group;

    protected string $template;

    protected static string $templateDefault = <<<EOF
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

    public function __construct(string $group = 'sidebar', string $template = null)
    {
        $this->group = $group;
        $this->template = empty($template) ? static::$templateDefault : $template;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $sections): string
    {
        $result = '';

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

    public function getReplacements(array $section): array
    {
        if (isset($section['view'])) {
            $sectionView = "{% include '{$section['view']}' %}";
        } elseif (isset($section['template'])) {
            $sectionView = $section['template'];
        } else {
            $sectionView = implode('', array_map(function ($item) {
                return '{{ form_row(form.' . $item . ') }}';
            }, $section['fields']));
        }

        return [
            '{sectionId}' => $section['id'],
            '{sectionLabel}' => $section['label'],
            '{sectionTranslationDomain}' => $section['translation_domain'],
            '{sectionFields}' => "{% set fields = ['" . implode("', '", $section['fields']) . "'] %}",
            '{sectionView}' => $sectionView,
            '{sectionActiveTab}' => $this->isSectionActive($section['id']) ? '' : ' collapsed',
            '{sectionActiveContent}' => $this->isSectionActive($section['id']) ? ' show' : '',
        ];
    }

    public function getTemplate(): string
    {
        return $this->template;
    }
}
