<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Form\SymfonyForm\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;

/**
 * @author Adam Banaszkiewicz
 */
class FormSkeletorExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'form_extension_manager' => null,
            'form_type_instance' => null,
        ]);

        $resolver->setAllowedTypes('form_extension_manager', ['null', ManagerInterface::class]);
        $resolver->setAllowedTypes('form_type_instance', ['null', FormTypeInterface::class]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['form_type_instance'] = $form->getConfig()->getOptions()['form_type_instance'];
    }
}
