<?php

declare(strict_types=1);

namespace Tulia\Cms\User\UserInterface\Web\Form\MyAccount;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Tulia\Component\FormBuilder\Form\AbstractFormSkeletonType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

/**
 * @author Adam Banaszkiewicz
 */
class MyAccountForm extends AbstractFormSkeletonType
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
    }
}
