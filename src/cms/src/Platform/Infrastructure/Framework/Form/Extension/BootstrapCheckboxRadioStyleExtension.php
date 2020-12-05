<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * @author Adam Banaszkiewicz
 */
class BootstrapCheckboxRadioStyleExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $type = $form->getConfig()->getType()->getInnerType();

        switch(true) {
            case $type instanceof CheckboxType: $classname = 'checkbox-custom'; break;
            case $type instanceof RadioType: $classname = 'switch-custom'; break;
            default: $classname = null;
        }

        if ($classname) {
            $view->vars['label_attr'] = array_merge($view->vars['label_attr'], [
                'class' => $classname,
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [CheckboxType::class, RadioType::class];
    }
}
