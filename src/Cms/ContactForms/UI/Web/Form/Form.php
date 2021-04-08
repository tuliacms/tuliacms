<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UI\Web\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Tulia\Cms\ContactForms\UI\Web\Form\Transformer\ReceiversTransformer;
use Tulia\Cms\ContactForms\UI\Web\Form\Type\FormFieldType;
use Tulia\Cms\Platform\Infrastructure\Framework\Form\FormType;

/**
 * @author Adam Banaszkiewicz
 */
class Form extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', Type\HiddenType::class)
            ->add('receivers', Type\TextareaType::class, [
                'label' => false,
                'help' => 'receiversHelpText',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('sender_name', Type\TextType::class)
            ->add('sender_email', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            ->add('reply_to', Type\TextType::class, [
                'constraints' => [
                    new Assert\Email(),
                ],
            ])
            ->add('name', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('subject', Type\TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('message_template', Type\TextareaType::class, [
                'label' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('fields_template', Type\TextareaType::class, [
                'label' => false,
            ])
            ->add('cancel', FormType\CancelType::class, [
                'route' => 'backend.form.homepage',
            ])
            ->add('save', FormType\SubmitType::class)
        ;

        $builder->get('receivers')->addModelTransformer(new ReceiversTransformer());
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('translation_domain', 'forms');
    }
}
