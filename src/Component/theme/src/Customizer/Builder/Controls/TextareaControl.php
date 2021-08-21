<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Controls;

/**
 * @author Adam Banaszkiewicz
 */
class TextareaControl extends AbstractControl
{
    public function build(array $params): string
    {
        return '<div class="form-group">
            <label>' . $this->trans($params['label'], [], $params['translation_domain']) . '</label>
            <textarea id="' . $params['control_id'] . '" name="' . $params['control_name'] . '" class="customizer-control form-control" data-transport="' . $params['transport'] . '">' . $this->escapeAttribute($params['value']) . '</textarea>
        </div>';
    }

    public static function getName(): string
    {
        return 'textarea';
    }
}
