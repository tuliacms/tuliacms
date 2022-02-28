<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Rendering\Controls;

/**
 * @author Adam Banaszkiewicz
 */
class TextControl extends AbstractControl
{
    public function build(array $params): string
    {
        return '<div class="form-group mb-3' . ($params['is_multilingual'] ? ' form-group-multilingual' : '') . '">
            <label class="customizer-label">' . $this->trans($params['label'], [], $params['translation_domain']) . '</label>
            <input type="text" id="' . $params['control_id'] . '" name="' . $params['control_name'] . '" class="customizer-control form-control" value="' . $this->escapeAttribute($params['value']) . '" data-transport="' . $params['transport'] . '" />
        </div>';
    }

    public static function getName(): string
    {
        return 'text';
    }
}
