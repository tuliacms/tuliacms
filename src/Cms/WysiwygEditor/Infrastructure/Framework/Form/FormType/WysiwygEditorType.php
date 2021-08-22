<?php

declare(strict_types=1);

namespace Tulia\Cms\WysiwygEditor\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Adam Banaszkiewicz
 */
class WysiwygEditorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label'] = false;
    }

    public function getBlockPrefix(): string
    {
        return 'wysiwyg_editor';
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}
