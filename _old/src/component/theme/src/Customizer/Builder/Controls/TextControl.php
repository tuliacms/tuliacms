<?php

declare(strict_types=1);

namespace Tulia\Component\Theme\Customizer\Builder\Controls;

/**
 * @author Adam Banaszkiewicz
 */
class TextControl extends AbstractControl
{
    /**
     * {@inheritdoc}
     */
    public function build(array $params): string
    {
        return '<div class="form-group'.($params['multilingual'] ? ' form-group-multilingual' : '').'">
            <label class="customizer-label">'.$params['label'].'</label>
            <input type="text" id="'.$params['control_id'].'" name="'.$params['control_name'].'" class="customizer-control form-control" value="'.$this->escapeAttribute($params['value']).'" data-transport="'.$params['transport'].'" />
        </div>';
    }

    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'text';
    }
}
