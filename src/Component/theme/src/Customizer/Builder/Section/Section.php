<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Section;

/**
 * @author Adam Banaszkiewicz
 */
class Section implements SectionInterface
{
    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param string $id
     * @param array $params
     */
    public function __construct(string $id, array $params = [])
    {
        $this->params = array_merge([
            'id'       => $id,
            'label'    => '',
            'priority' => 0,
            'parent'   => null,
            'translation_domain' => false,
        ], $params);
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed|null
     */
    public function __call(string $name, array $arguments = [])
    {
        return $this->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $name, $default = null)
    {
        return $this->params[$name] ?? $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $name, $value): void
    {
        $this->params[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $controls, array $sections): string
    {
        $id = str_replace('.', '_', $this->get('id'));
        $parentId = str_replace('.', '_', $this->get('parent') ? $this->get('parent') : 'home');
        $sectionsList = [];

        if ($sections !== []) {
            $sectionsList[] = '<div class="controls-list">';

            foreach ($sections as $section) {
                $sectionId = str_replace('.', '_', $section->get('id'));
                $sectionsList[] = '<div class="control-trigger" data-show-pane="' . $sectionId . '">' . $section->get('label') . '</div>';
            }

            $sectionsList[] = '</div>';
        }

        return '<div class="control-pane control-pane-name-' . $id . '" data-section="' . $id . '">
            <div class="control-pane-headline">
                <button type="button" class="control-pane-back" data-show-pane="' . $parentId . '"><i class="icon fas fa-chevron-left"></i></button>
                <h4>' . $this->get('label') . '</h4>
            </div>
            ' . implode('', $sectionsList) . '
            <div class="control-pane-content">
                ' . $controls . '
            </div>
        </div>';
    }
}
