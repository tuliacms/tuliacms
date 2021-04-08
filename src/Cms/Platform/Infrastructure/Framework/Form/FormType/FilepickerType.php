<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FilepickerType extends TextType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'block_prefix' => 'filepicker',
            'filter.type' => '*'
        ]);

        $resolver->addAllowedTypes('filter.type', ['string', 'array']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['filter'] = [
            'type' => $options['filter.type'],
        ];
    }
}
