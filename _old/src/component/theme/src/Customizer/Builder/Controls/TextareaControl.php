<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Controls;

/**
 * @author Adam Banaszkiewicz
 */
class TextareaControl extends AbstractControl
{
    /**
     * {@inheritdoc}
     */
    public function build(array $params): string
    {
        return '<div class="form-group">
            <label>'.$params['label'].'</label>
            <textarea id="'.$params['control_id'].'" name="'.$params['control_name'].'" class="customizer-control form-control" data-transport="'.$params['transport'].'">'.$this->escapeAttribute($params['value']).'</textarea>
        </div>';
    }

    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'textarea';
    }
}
