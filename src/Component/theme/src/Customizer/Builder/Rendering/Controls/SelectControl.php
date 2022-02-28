<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Rendering\Controls;

/**
 * @author Adam Banaszkiewicz
 */
class SelectControl extends AbstractControl
{
    /**
     * {@inheritdoc}
     */
    public function build(array $params): string
    {
        $values = '';

        foreach ($params['choices'] ?? [] as $id => $val) {
            $selected = $params['value'] === $id;

            $values .= '<option value="' . $this->escapeAttribute($id) . '"' . ($selected ? ' selected="selected"' : '') . '>' . $this->escapeAttribute($val) . '</option>';
        }

        return '<div class="form-group mb-3">
            <label>' . $this->trans($params['label'], [], $params['translation_domain']) . '</label>
            <select id="' . $params['control_id'] . '" name="' . $params['control_name'] . '" class="customizer-control form-control" data-transport="'.$params['transport'].'">'.$values.'</select>
        </div>';
    }

    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'select';
    }
}
