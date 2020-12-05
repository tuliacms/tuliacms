<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UI\Web\Form\MyAccount;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tulia\Component\FormBuilder\Manager\ManagerInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

/**
 * @author Adam Banaszkiewicz
 */
class MyAccountForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', Type\HiddenType::class)
            ->add('save', FormType\SubmitType::class)
        ;

        if ($options['form_extension_manager'] instanceof ManagerInterface) {
            $options['form_extension_manager']->buildForm($builder, $options);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'form_extension_manager' => null,
        ]);
    }
}
