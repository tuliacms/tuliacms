<?php

declare(strict_types=1);

namespace Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Adam Banaszkiewicz
 */
class YesNoType extends ChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['choices'] = [
            'yes' => '1',
            'no'  => '0',
        ];
        $options['constraints'] = [
            new Assert\NotBlank(),
            new Assert\Choice([ 'choices' => [
                'yes' => '1',
                'no'  => '0',
            ]]),
        ];
        $options['choice_translation_domain'] = 'messages';

        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $options['choice_translation_domain'] = 'messages';

        parent::buildView($view, $form, $options);
    }
}
