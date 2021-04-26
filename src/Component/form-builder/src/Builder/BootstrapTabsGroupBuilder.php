<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Builder;

use Tulia\Component\FormBuilder\Section\SectionInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BootstrapTabsGroupBuilder extends AbstractGroupBuilder
{
    /**
     * @var string
     */
    protected $group;

    /**
     * @var string
     */
    protected $tabTemplate;

    /**
     * @var string
     */
    protected static $tabTemplateDefault = <<<EOF
<li class="nav-item">
    <a class="nav-link{sectionActive}" data-toggle="tab" href="#tab-{sectionId}">
        {{ '{sectionLabel}'|trans({}, '{sectionTranslationDomain}') }}
    </a>
</li>
EOF
    ;

    /**
     * @var string
     */
    protected static $contentTemplateDefault = <<<EOF
<div class="tab-pane fade{sectionActive}" id="tab-{sectionId}">{sectionView}</div>
EOF
    ;

    /**
     * @param string $group
     * @param string|null $tabTemplate
     */
    public function __construct(string $group = 'default', string $tabTemplate = null)
    {
        $this->group       = $group;
        $this->tabTemplate = empty($tabTemplate) ? static::$tabTemplateDefault : $tabTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function build(array $sections): string
    {
        $tabs = '';
        $contents = '';

        /** @var SectionInterface $section */
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

    public function getTabReplacements(SectionInterface $section): array
    {
        return [
            '{sectionId}' => $section->getId(),
            '{sectionLabel}' => $section->getLabel(),
            '{sectionFields}' => $section->getFieldsStatement(),
            '{sectionTranslationDomain}' => $section->getTranslationDomain() ?? 'messages',
            '{sectionActive}' => $this->isSectionActive($section->getId()) ? ' active' : '',
        ];
    }

    public function getContentReplacements(SectionInterface $section): array
    {
        return [
            '{sectionId}' => $section->getId(),
            '{sectionView}' => $section->getViewStatement(),
            '{sectionActive}' => $this->isSectionActive($section->getId()) ? ' show active' : '',
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
