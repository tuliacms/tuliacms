<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Builder;

/**
 * @author Adam Banaszkiewicz
 */
class BootstrapTabsGroupBuilder extends AbstractGroupBuilder
{
    protected string $group;

    protected string $tabTemplate;

    protected static string $tabTemplateDefault = <<<EOF
<li class="nav-item">
    <a class="nav-link{sectionActive}" data-toggle="tab" href="#tab-{sectionId}">
        {{ '{sectionLabel}'|trans({}, '{sectionTranslationDomain}') }}
    </a>
</li>
EOF
    ;

    protected static string $contentTemplateDefault = <<<EOF
<div class="tab-pane fade{sectionActive}" id="tab-{sectionId}">{sectionView}</div>
EOF
    ;

    public function __construct(string $group = 'default', string $tabTemplate = null)
    {
        $this->group = $group;
        $this->tabTemplate = empty($tabTemplate) ? static::$tabTemplateDefault : $tabTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $sections): string
    {
        $tabs = '';
        $contents = '';

        foreach ($sections as $section) {
            $replacements = $this->getTabReplacements($section);

            $tabs .= str_replace(
                array_keys($replacements),
                array_values($replacements),
                $this->getTabTemplate()
            );

            $replacements = $this->getContentReplacements($section);

            $contents .= str_replace(
                array_keys($replacements),
                array_values($replacements),
                $this->getContentTemplate()
            );
        }

        return '<ul class="nav nav-tabs page-form-tabs" role="tablist">' . $tabs . '</ul><div class="tab-content">' . $contents . '</div>';
    }

    /**
     * {@inheritdoc}
     */
    public function supportsGroup(?string $group): bool
    {
        return $this->group === $group;
    }

    public function getTabReplacements(array $section): array
    {
        return [
            '{sectionId}' => $section['id'],
            '{sectionLabel}' => $section['label'],
            '{sectionFields}' => "{% set fields = ['" . implode("', '", $section['fields']) . "'] %}",
            '{sectionTranslationDomain}' => $section['translation_domain'],
            '{sectionActive}' => $this->isSectionActive($section['id']) ? ' active' : '',
        ];
    }

    public function getContentReplacements(array $section): array
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
            '{sectionView}' => $sectionView,
            '{sectionActive}' => $this->isSectionActive($section['id']) ? ' show active' : '',
        ];
    }

    public function getTabTemplate(): string
    {
        return $this->tabTemplate;
    }

    public function getContentTemplate(): string
    {
        return static::$contentTemplateDefault;
    }
}
