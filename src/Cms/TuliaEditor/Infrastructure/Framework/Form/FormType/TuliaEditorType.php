<?php

declare(strict_types=1);

namespace Tulia\Cms\TuliaEditor\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Cms\Attributes\Domain\WriteModel\Model\AttributesAwareInterface;

/**
 * @author Adam Banaszkiewicz
 */
class TuliaEditorType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false
        ]);

        $resolver->setRequired('entity');
        $resolver->setAllowedTypes('entity', [AttributesAwareInterface::class]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['label'] = false;
        $view->vars['entity'] = $options['entity'];
    }

    public function getBlockPrefix(): string
    {
        return 'tulia_editor';
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}
